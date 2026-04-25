<?php

namespace App\Console\Commands;

use App\Models\Commune;
use App\Models\Pays;
use App\Models\Province;
use App\Models\Secteur;
use App\Models\Territoire;
use App\Models\Ville;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Throwable;

class ImportLocalisationData extends Command
{
    protected $signature   = 'import:localisation';
    protected $description = 'Importe provinces, territoires, secteurs, villes et communes depuis les fichiers Excel';

    public function handle(): int
    {
        // ── Récupérer le pays COD ────────────────────────────────────────────
        $pays = Pays::where('code', 'COD')->first();

        if (!$pays) {
            $this->error('Pays COD introuvable. Lance d\'abord : php artisan import:pays');
            return self::FAILURE;
        }

        $this->info("Pays trouvé : {$pays->designation} (id={$pays->id})");

        $errors = 0;

        // ── 1. territoires_et_secteurs.xlsx ──────────────────────────────────
        $errors += $this->importTerritoiresSecteurs($pays);

        // ── 2. localisation.xlsx ─────────────────────────────────────────────
        $errors += $this->importVillesCommunes($pays);

        // ── Rapport final ────────────────────────────────────────────────────
        if ($errors > 0) {
            $this->warn("{$errors} erreur(s) — consultez storage/logs/laravel.log");
            return self::FAILURE;
        }

        $this->info('Import terminé avec succès.');
        return self::SUCCESS;
    }

    // ────────────────────────────────────────────────────────────────────────
    // Fichier 1 : territoires_et_secteurs.xlsx
    // Colonnes  : A=Province, B=Territoire, C=Secteur
    // ────────────────────────────────────────────────────────────────────────
    private function importTerritoiresSecteurs(Pays $pays): int
    {
        $file = base_path('territoires_et_secteurs.xlsx');

        if (!file_exists($file)) {
            $this->error("Fichier introuvable : $file");
            return 1;
        }

        $this->info("\n[1/2] Lecture de territoires_et_secteurs.xlsx...");

        try {
            $sheet = IOFactory::load($file)->getActiveSheet();
        } catch (Throwable $e) {
            $this->error('Impossible de lire le fichier : ' . $e->getMessage());
            Log::error('[import:localisation] Lecture territoires_et_secteurs.xlsx', ['exception' => $e->getMessage()]);
            return 1;
        }

        $highestRow = $sheet->getHighestRow();
        $errors     = 0;

        $currentProvince   = null;
        $currentTerritoire = null;

        // Compteurs
        $provinces  = ['new' => 0, 'existing' => 0];
        $territoires = ['new' => 0, 'existing' => 0];
        $secteurs   = ['new' => 0, 'existing' => 0];

        $bar = $this->output->createProgressBar($highestRow - 1);
        $bar->start();

        for ($row = 2; $row <= $highestRow; $row++) {
            $colProvince   = trim((string) $sheet->getCell("A{$row}")->getValue());
            $colTerritoire = trim((string) $sheet->getCell("B{$row}")->getValue());
            $colSecteur    = trim((string) $sheet->getCell("C{$row}")->getValue());

            // Reconstituer les cellules fusionnées (valeur héritée)
            if ($colProvince !== '') {
                $currentProvince   = $colProvince;
                $currentTerritoire = null; // reset territoire quand province change
            }

            if ($colTerritoire !== '') {
                $currentTerritoire = $colTerritoire;
            }

            // Ignorer les lignes sans données utiles
            if (!$currentProvince || (!$currentTerritoire && !$colSecteur)) {
                $bar->advance();
                continue;
            }

            try {
                DB::transaction(function () use (
                    $pays, $currentProvince, $currentTerritoire, $colSecteur,
                    &$provinces, &$territoires, &$secteurs
                ) {
                    // Province
                    [$provinceModel, $created] = $this->firstOrCreateTracked(
                        Province::class,
                        ['designation' => $currentProvince],
                        ['pays_id'     => $pays->id]
                    );
                    $created ? $provinces['new']++ : $provinces['existing']++;

                    if (!$currentTerritoire) return;

                    // Territoire
                    [$territoireModel, $created] = $this->firstOrCreateTracked(
                        Territoire::class,
                        ['province_id' => $provinceModel->id, 'designation' => $currentTerritoire],
                        []
                    );
                    $created ? $territoires['new']++ : $territoires['existing']++;

                    if (!$colSecteur) return;

                    // Secteur
                    [, $created] = $this->firstOrCreateTracked(
                        Secteur::class,
                        ['territoire_id' => $territoireModel->id, 'designation' => $colSecteur],
                        []
                    );
                    $created ? $secteurs['new']++ : $secteurs['existing']++;
                });
            } catch (Throwable $e) {
                $errors++;
                Log::error('[import:localisation] Erreur ligne ' . $row, [
                    'province'   => $currentProvince,
                    'territoire' => $currentTerritoire,
                    'secteur'    => $colSecteur,
                    'exception'  => $e->getMessage(),
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->table(
            ['', 'Nouveaux', 'Existants'],
            [
                ['Provinces',   $provinces['new'],   $provinces['existing']],
                ['Territoires', $territoires['new'],  $territoires['existing']],
                ['Secteurs',    $secteurs['new'],     $secteurs['existing']],
            ]
        );

        return $errors;
    }

    // ────────────────────────────────────────────────────────────────────────
    // Fichier 2 : localisation.xlsx
    // Bloc gauche  : A=Province, B=Territoire (ignoré ici, déjà importé)
    // Bloc droit   : F=Province, G=Ville, H=Commune
    // ────────────────────────────────────────────────────────────────────────
    private function importVillesCommunes(Pays $pays): int
    {
        $file = base_path('localisation.xlsx');

        if (!file_exists($file)) {
            $this->error("Fichier introuvable : $file");
            return 1;
        }

        $this->info("\n[2/2] Lecture de localisation.xlsx...");

        try {
            $sheet = IOFactory::load($file)->getActiveSheet();
        } catch (Throwable $e) {
            $this->error('Impossible de lire le fichier : ' . $e->getMessage());
            Log::error('[import:localisation] Lecture localisation.xlsx', ['exception' => $e->getMessage()]);
            return 1;
        }

        $highestRow = $sheet->getHighestRow();
        $errors     = 0;

        $currentProvince = null;
        $currentVille    = null;

        $provinces = ['new' => 0, 'existing' => 0];
        $villes    = ['new' => 0, 'existing' => 0];
        $communes  = ['new' => 0, 'existing' => 0];

        $bar = $this->output->createProgressBar($highestRow - 1);
        $bar->start();

        for ($row = 2; $row <= $highestRow; $row++) {
            $colProvince = trim((string) $sheet->getCell("F{$row}")->getValue());
            $colVille    = trim((string) $sheet->getCell("G{$row}")->getValue());
            $colCommune  = trim((string) $sheet->getCell("H{$row}")->getValue());

            // Reconstituer les cellules fusionnées
            if ($colProvince !== '') {
                $currentProvince = $colProvince;
                $currentVille    = null;
            }

            if ($colVille !== '') {
                $currentVille = $colVille;
            }

            if (!$currentProvince || !$currentVille || !$colCommune) {
                $bar->advance();
                continue;
            }

            try {
                DB::transaction(function () use (
                    $pays, $currentProvince, $currentVille, $colCommune,
                    &$provinces, &$villes, &$communes
                ) {
                    // Province (updateOrCreate pour dédupliquer avec fichier 1)
                    [$provinceModel, $created] = $this->firstOrCreateTracked(
                        Province::class,
                        ['designation' => $currentProvince],
                        ['pays_id'     => $pays->id]
                    );
                    $created ? $provinces['new']++ : $provinces['existing']++;

                    // Ville
                    [$villeModel, $created] = $this->firstOrCreateTracked(
                        Ville::class,
                        ['province_id' => $provinceModel->id, 'designation' => $currentVille],
                        []
                    );
                    $created ? $villes['new']++ : $villes['existing']++;

                    // Commune
                    [, $created] = $this->firstOrCreateTracked(
                        Commune::class,
                        ['ville_id'    => $villeModel->id, 'designation' => $colCommune],
                        []
                    );
                    $created ? $communes['new']++ : $communes['existing']++;
                });
            } catch (Throwable $e) {
                $errors++;
                Log::error('[import:localisation] Erreur ligne ' . $row, [
                    'province'  => $currentProvince,
                    'ville'     => $currentVille,
                    'commune'   => $colCommune,
                    'exception' => $e->getMessage(),
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->table(
            ['', 'Nouveaux', 'Existants'],
            [
                ['Provinces', $provinces['new'], $provinces['existing']],
                ['Villes',    $villes['new'],    $villes['existing']],
                ['Communes',  $communes['new'],  $communes['existing']],
            ]
        );

        return $errors;
    }

    /**
     * firstOrCreate avec tracking création/existant.
     * Retourne [$model, $wasCreated].
     */
    private function firstOrCreateTracked(string $model, array $search, array $extra): array
    {
        $existing = $model::where($search)->first();

        if ($existing) {
            return [$existing, false];
        }

        return [$model::create(array_merge($search, $extra)), true];
    }
}
