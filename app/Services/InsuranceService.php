<?php

namespace App\Services;

use App\Models\Insurance;
use App\Models\InsuranceClaim;
use App\Models\Reference\InsuranceStatus;

class InsuranceService
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function createSubscription(array $data): Insurance
    {
        $status = InsuranceStatus::where('code', 'en_attente')->firstOrFail();

        return Insurance::create([
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
    }

    public function activateInsurance(Insurance $insurance, int $processedBy): Insurance
    {
        $status = InsuranceStatus::where('code', 'active')->firstOrFail();

        $insurance->update([
            'insurance_status_id' => $status->id,
            'date_debut'          => now()->toDateString(),
            'date_fin'            => now()->addYear()->toDateString(),
            'prochaine_echeance'  => now()->addYear()->toDateString(),
            'traite_par'          => $processedBy,
        ]);

        $this->notificationService->create([
            'user_id'  => $insurance->client->user_id,
            'category' => 'app',
            'type'     => 'insurance',
            'title'    => 'Assurance activée',
            'body'     => 'Votre assurance est maintenant active.',
        ]);

        return $insurance->fresh('status');
    }

    public function createClaim(array $data): InsuranceClaim
    {
        return InsuranceClaim::create([
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

    public function approveClaim(InsuranceClaim $claim, float $amount, int $processedBy): InsuranceClaim
    {
        $claim->update([
            'montant_approuve' => $amount,
            'statut'           => 'approuve',
            'traite_par'       => $processedBy,
        ]);

        $this->notificationService->create([
            'user_id'  => $claim->client->user_id,
            'category' => 'app',
            'type'     => 'insurance',
            'title'    => 'Réclamation approuvée',
            'body'     => 'Votre réclamation a été approuvée.',
        ]);

        return $claim->fresh();
    }

    public function rejectClaim(InsuranceClaim $claim, int $processedBy): InsuranceClaim
    {
        $claim->update(['statut' => 'rejete', 'traite_par' => $processedBy]);

        return $claim->fresh();
    }
}
