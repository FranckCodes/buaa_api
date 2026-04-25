<?php

namespace App\Console\Commands;

use App\Models\Pays;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class ImportPaysData extends Command
{
    protected $signature   = 'import:pays';
    protected $description = 'Importe les pays depuis l\'API iso.lahrim.fr';

    public function handle(): int
    {
        $this->info('Importation des pays en cours...');

        // ── 1. Appel API ────────────────────────────────────────────────────
        try {
            $response = Http::withoutVerifying()
                ->timeout(15)
                ->get('https://iso.lahrim.fr/countries');
        } catch (Throwable $e) {
            $this->error('Erreur réseau : ' . $e->getMessage());
            Log::error('[import:pays] Erreur réseau', ['exception' => $e->getMessage()]);
            return self::FAILURE;
        }

        if ($response->failed()) {
            $this->error('L\'API a retourné une erreur HTTP ' . $response->status());
            Log::error('[import:pays] Réponse HTTP échouée', [
                'status'   => $response->status(),
                'body'     => $response->body(),
            ]);
            return self::FAILURE;
        }

        // ── 2. Validation de la structure JSON ───────────────────────────────
        $data = $response->json('data');

        if (!is_array($data) || empty($data)) {
            $this->warn('Aucune donnée reçue ou format inattendu.');
            Log::warning('[import:pays] Données vides ou format invalide', [
                'body' => $response->body(),
            ]);
            return self::FAILURE;
        }

        // ── 3. Import avec barre de progression ──────────────────────────────
        $total   = count($data);
        $success = 0;
        $errors  = 0;
        $skipped = 0;

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($data as $index => $item) {
            // Validation des champs requis
            if (empty($item['alpha3']) || empty($item['name_fr'])) {
                $this->newLine();
                $this->warn("Entrée ignorée (index {$index}) : champs manquants — " . json_encode($item));
                Log::warning('[import:pays] Entrée ignorée', ['index' => $index, 'item' => $item]);
                $skipped++;
                $bar->advance();
                continue;
            }

            try {
                Pays::updateOrCreate(
                    ['code'        => $item['alpha3']],
                    [
                        'designation' => $item['name_fr'],
                        'picture'     => $item['alpha3'],
                    ]
                );
                $success++;
            } catch (Throwable $e) {
                $errors++;
                $this->newLine();
                $this->error("Erreur sur {$item['alpha3']} : " . $e->getMessage());
                Log::error('[import:pays] Erreur insertion', [
                    'code'      => $item['alpha3'],
                    'exception' => $e->getMessage(),
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        // ── 4. Rapport final ─────────────────────────────────────────────────
        $this->table(
            ['Total', 'Importés', 'Ignorés', 'Erreurs'],
            [[$total, $success, $skipped, $errors]]
        );

        if ($errors > 0) {
            $this->warn("{$errors} erreur(s) — consultez storage/logs/laravel.log pour les détails.");
            Log::warning('[import:pays] Import terminé avec erreurs', compact('total', 'success', 'skipped', 'errors'));
            return self::FAILURE;
        }

        $this->info('Import terminé avec succès.');
        Log::info('[import:pays] Import terminé', compact('total', 'success', 'skipped'));

        return self::SUCCESS;
    }
}
