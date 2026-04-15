<?php

namespace App\Services;

use App\Models\Adhesion;
use App\Models\AdhesionRequest;
use App\Models\Client;
use App\Models\Cotisation;
use App\Models\Reference\AdhesionStatus;
use App\Models\Union;
use Illuminate\Support\Facades\DB;

class AdhesionService
{
    public function __construct(
        protected IdGeneratorService $idGenerator,
        protected NotificationService $notificationService
    ) {}

    public function createRequest(array $data): AdhesionRequest
    {
        return AdhesionRequest::create([
            'nom'                      => $data['nom'],
            'demandeur_type'           => $data['demandeur_type'],
            'client_activity_type_id'  => $data['client_activity_type_id'] ?? null,
            'client_structure_type_id' => $data['client_structure_type_id'] ?? null,
            'representant'             => $data['representant'] ?? null,
            'telephone'                => $data['telephone'],
            'email'                    => $data['email'] ?? null,
            'adresse'                  => $data['adresse'] ?? null,
            'province'                 => $data['province'] ?? null,
            'date_demande'             => $data['date_demande'] ?? now()->toDateString(),
            'cotisation'               => $data['cotisation'] ?? null,
            'statut'                   => 'en_attente',
            'membres_nombre'           => $data['membres_nombre'] ?? null,
            'superficie_totale'        => $data['superficie_totale'] ?? null,
            'type_culture'             => $data['type_culture'] ?? null,
            'experience_annees'        => $data['experience_annees'] ?? null,
            'nombre_animaux'           => $data['nombre_animaux'] ?? null,
            'type_elevage'             => $data['type_elevage'] ?? null,
        ]);
    }

    public function approveRequest(
        AdhesionRequest $request,
        Client $client,
        Union $union,
        int $adhesionTypeId,
        ?int $paymentModeId,
        int $processedBy
    ): Adhesion {
        return DB::transaction(function () use ($request, $client, $union, $adhesionTypeId, $paymentModeId, $processedBy) {
            $status = AdhesionStatus::where('code', 'actif')->firstOrFail();

            $adhesion = Adhesion::create([
                'client_id'          => $client->id,
                'union_id'           => $union->id,
                'adhesion_type_id'   => $adhesionTypeId,
                'adhesion_status_id' => $status->id,
                'numero_membre'      => $this->idGenerator->generateMembershipNumber(),
                'date_adhesion'      => now()->toDateString(),
                'prochaine_echeance' => now()->addYear()->toDateString(),
                'cotisation_initiale' => $request->cotisation ?? 0,
                'cotisation_annuelle' => $request->cotisation ?? 0,
                'payment_mode_id'    => $paymentModeId,
            ]);

            Cotisation::create([
                'adhesion_id'    => $adhesion->id,
                'annee'          => now()->year,
                'montant'        => $adhesion->cotisation_initiale,
                'statut'         => 'en_attente',
                'payment_mode_id' => $paymentModeId,
            ]);

            $request->update(['statut' => 'approuve', 'traite_par' => $processedBy]);

            $this->notificationService->create([
                'user_id'  => $client->user_id,
                'category' => 'app',
                'type'     => 'membership',
                'title'    => 'Adhésion approuvée',
                'body'     => "Votre demande d'adhésion a été approuvée.",
            ]);

            return $adhesion->load('client', 'union', 'status', 'type');
        });
    }

    public function rejectRequest(AdhesionRequest $request, int $processedBy): AdhesionRequest
    {
        $request->update(['statut' => 'rejete', 'traite_par' => $processedBy]);

        return $request->fresh();
    }

    public function suspendAdhesion(Adhesion $adhesion): Adhesion
    {
        $status = AdhesionStatus::where('code', 'suspendu')->firstOrFail();
        $adhesion->update(['adhesion_status_id' => $status->id]);

        return $adhesion->fresh('status');
    }
}
