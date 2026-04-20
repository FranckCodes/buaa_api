<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\Insurance;
use App\Models\InsuranceBeneficiary;
use App\Models\InsuranceClaim;
use App\Models\Reference\InsuranceStatus;
use Illuminate\Support\Facades\DB;

class InsuranceService
{
    public function __construct(
        protected IdGeneratorService $idGenerator,
        protected NotificationService $notificationService
    ) {}

    public function createSubscription(array $data): Insurance
    {
        $status = InsuranceStatus::where('code', 'en_attente')->firstOrFail();

        return DB::transaction(function () use ($data, $status) {
            $insurance = Insurance::create([
                'id'                  => $this->idGenerator->generateInsuranceId(),
                'client_id'           => $data['client_id'],
                'insurance_type_id'   => $data['insurance_type_id'],
                'insurance_status_id' => $status->id,
                'montant_annuel'      => $data['montant_annuel'],
                'date_souscription'   => $data['date_souscription'] ?? now()->toDateString(),
                'description'         => $data['description'] ?? null,
                'couvertures'         => $data['couvertures'] ?? null,
                'etablissement'       => $data['etablissement'] ?? null,
                'niveau_etude'        => $data['niveau_etude'] ?? null,
                'superficie_hectares' => $data['superficie_hectares'] ?? null,
                'type_culture'        => $data['type_culture'] ?? null,
                'valeur_materiel'     => $data['valeur_materiel'] ?? null,
                'antecedents_medicaux' => $data['antecedents_medicaux'] ?? null,
                'medecin_traitant'    => $data['medecin_traitant'] ?? null,
            ]);

            foreach ($data['beneficiaries'] ?? [] as $b) {
                InsuranceBeneficiary::create([
                    'insurance_id' => $insurance->id,
                    'nom'          => $b['nom'],
                    'age'          => $b['age'] ?? null,
                    'relation'     => $b['relation'] ?? null,
                ]);
            }

            return $insurance->load('beneficiaries', 'type', 'status', 'client');
        });
    }

    public function activateInsurance(Insurance $insurance, string $processedBy): Insurance
    {
        if ($insurance->status?->code === 'active') {
            throw new BusinessException('Cette assurance est déjà active.', 422);
        }

        $status = InsuranceStatus::where('code', 'active')->firstOrFail();

        $insurance->update([
            'insurance_status_id' => $status->id,
            'date_debut'          => now()->toDateString(),
            'date_fin'            => now()->addYear()->toDateString(),
            'prochaine_echeance'  => now()->addYear()->toDateString(),
            'traite_par'          => $processedBy,
        ]);

        $this->notificationService->create([
            'user_id'  => $insurance->client_id,
            'category' => 'app',
            'type'     => 'insurance',
            'title'    => 'Assurance activée',
            'body'     => 'Votre assurance est maintenant active.',
        ]);

        return $insurance->fresh(['status', 'type', 'client', 'beneficiaries']);
    }

    public function createClaim(array $data): InsuranceClaim
    {
        return InsuranceClaim::create([
            'id'              => $this->idGenerator->generateClaimId(),
            'insurance_id'    => $data['insurance_id'],
            'client_id'       => $data['client_id'],
            'type_sinistre'   => $data['type_sinistre'],
            'montant_reclame' => $data['montant_reclame'],
            'statut'          => 'en_analyse',
            'description'     => $data['description'] ?? null,
            'date_sinistre'   => $data['date_sinistre'] ?? null,
            'date_soumission' => $data['date_soumission'] ?? now()->toDateString(),
        ]);
    }

    public function approveClaim(InsuranceClaim $claim, float $amount, string $processedBy): InsuranceClaim
    {
        if (in_array($claim->statut, ['approuve', 'rembourse'])) {
            throw new BusinessException('Cette réclamation est déjà traitée positivement.', 422);
        }

        $claim->update([
            'montant_approuve' => $amount,
            'statut'           => 'approuve',
            'traite_par'       => $processedBy,
        ]);

        $this->notificationService->create([
            'user_id'  => $claim->client_id,
            'category' => 'app',
            'type'     => 'insurance',
            'title'    => 'Réclamation approuvée',
            'body'     => 'Votre réclamation a été approuvée.',
        ]);

        return $claim->fresh(['insurance', 'client', 'treatedBy']);
    }

    public function rejectClaim(InsuranceClaim $claim, string $processedBy): InsuranceClaim
    {
        if ($claim->statut === 'rejete') {
            throw new BusinessException('Cette réclamation est déjà rejetée.', 422);
        }

        $claim->update(['statut' => 'rejete', 'traite_par' => $processedBy]);

        $this->notificationService->create([
            'user_id'  => $claim->client_id,
            'category' => 'app',
            'type'     => 'insurance',
            'title'    => 'Réclamation rejetée',
            'body'     => 'Votre réclamation a été rejetée.',
        ]);

        return $claim->fresh(['insurance', 'client', 'treatedBy']);
    }
}
