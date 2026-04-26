<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\Adhesion;
use App\Models\AdhesionRequest;
use App\Models\AdhesionRequestValidation;
use App\Models\Client;
use App\Models\Cotisation;
use App\Models\Reference\AdhesionStatus;
use App\Models\Reference\UnionStatus;
use App\Models\Union;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdhesionService
{
    public function __construct(
        protected IdGeneratorService $idGenerator,
        protected NotificationService $notificationService
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    // UNIONS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Création d'une union par un Admin.
     * Statut par défaut = SUSPENDU (en attente de soumission des docs par le président).
     */
    public function createUnion(array $data): Union
    {
        $suspendu = UnionStatus::where('code', UnionStatus::SUSPENDU)->firstOrFail();

        return Union::create([
            'id'                   => $this->idGenerator->generateUnionId(),
            'nom'                  => $data['nom'],
            'type'                 => $data['type'],
            'union_status_id'      => $suspendu->id,
            'president_id'         => $data['president_id'],
            'province_id'          => $data['province_id'],
            'territoire_id'        => $data['territoire_id'] ?? null,
            'secteur_id'           => $data['secteur_id'] ?? null,
            'ville_id'             => $data['ville_id'] ?? null,
            'commune_id'           => $data['commune_id'] ?? null,
            'adresse'              => $data['adresse'] ?? null,
            'telephone'            => $data['telephone'] ?? null,
            'email'                => $data['email'] ?? null,
            'date_creation'        => $data['date_creation'] ?? null,
            'secretaire'           => $data['secretaire'] ?? null,
            'tresorier'            => $data['tresorier'] ?? null,
            'commissaire'          => $data['commissaire'] ?? null,
            'membres_total'        => $data['membres_total'] ?? 0,
            'superficie_totale'    => $data['superficie_totale'] ?? null,
            'cultures_principales' => $data['cultures_principales'] ?? null,
            'services'             => $data['services'] ?? null,
        ]);
    }

    /**
     * Le président soumet les documents officiels → l'union passe EN_REVUE.
     * Les fichiers eux-mêmes sont attachés via DocumentService (morph documentable).
     */
    public function submitUnionDocuments(Union $union): Union
    {
        if (! in_array($union->status?->code, [UnionStatus::SUSPENDU, UnionStatus::EN_REVUE], true)) {
            throw new BusinessException("Cette union ne peut plus être modifiée à ce stade.", 422);
        }

        $enRevue = UnionStatus::where('code', UnionStatus::EN_REVUE)->firstOrFail();
        $union->update(['union_status_id' => $enRevue->id]);

        return $union->fresh('status');
    }

    /**
     * Validation finale par l'Admin → l'union devient ACTIVE.
     */
    public function activateUnion(Union $union, User $admin): Union
    {
        if ($union->status?->code !== UnionStatus::EN_REVUE) {
            throw new BusinessException("L'union doit être en revue pour être activée.", 422);
        }

        $active = UnionStatus::where('code', UnionStatus::ACTIVE)->firstOrFail();
        $union->update([
            'union_status_id' => $active->id,
            'validated_by'    => $admin->id,
            'validated_at'    => now(),
        ]);

        if ($union->president_id) {
            $this->notificationService->create([
                'user_id'  => $union->president_id,
                'category' => 'app',
                'type'     => 'union_activated',
                'title'    => 'Union activée',
                'body'     => "Votre union « {$union->nom} » est désormais active.",
            ]);
        }

        return $union->fresh('status', 'validator');
    }

    /**
     * Suspension réversible (Admin).
     */
    public function suspendUnion(Union $union): Union
    {
        $suspendu = UnionStatus::where('code', UnionStatus::SUSPENDU)->firstOrFail();
        $union->update(['union_status_id' => $suspendu->id]);

        return $union->fresh('status');
    }

    /**
     * Désactivation définitive (Super Admin uniquement, irréversible).
     * L'autorisation est portée par UnionPolicy::deactivate via le before().
     */
    public function deactivateUnion(Union $union, User $superAdmin, string $reason): Union
    {
        if ($union->status?->code === UnionStatus::DESACTIVEE) {
            throw new BusinessException("Cette union est déjà désactivée.", 422);
        }

        $desactivee = UnionStatus::where('code', UnionStatus::DESACTIVEE)->firstOrFail();
        $union->update([
            'union_status_id'     => $desactivee->id,
            'deactivated_by'      => $superAdmin->id,
            'deactivated_at'      => now(),
            'deactivation_reason' => $reason,
        ]);

        return $union->fresh('status', 'deactivator');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // ADHESION REQUESTS
    // ─────────────────────────────────────────────────────────────────────────

    public function createRequest(array $data): AdhesionRequest
    {
        $union = Union::findOrFail($data['union_id']);

        if (! $union->isActive()) {
            throw new BusinessException("Cette union n'est pas active : aucune adhésion possible.", 422);
        }

        return DB::transaction(function () use ($data) {
            $request = AdhesionRequest::create([
                'id'                       => $this->idGenerator->generateAdhesionId(),
                'nom'                      => $data['nom'],
                'demandeur_type'           => $data['demandeur_type'],
                'union_id'                 => $data['union_id'],
                'client_id'                => $data['client_id'] ?? null,
                'client_activity_type_id'  => $data['client_activity_type_id'] ?? null,
                'client_structure_type_id' => $data['client_structure_type_id'] ?? null,
                'representant'             => $data['representant'] ?? null,
                'telephone'                => $data['telephone'],
                'email'                    => $data['email'] ?? null,
                'adresse'                  => $data['adresse'] ?? null,
                'province_id'              => $data['province_id'],
                'territoire_id'            => $data['territoire_id'] ?? null,
                'secteur_id'               => $data['secteur_id'] ?? null,
                'ville_id'                 => $data['ville_id'] ?? null,
                'commune_id'               => $data['commune_id'] ?? null,
                'date_demande'             => $data['date_demande'] ?? now()->toDateString(),
                'cotisation'               => $data['cotisation'] ?? null,
                'statut'                   => 'en_attente',
                'etape_courante'           => AdhesionRequest::ETAPE_EN_ATTENTE,
                'membres_nombre'           => $data['membres_nombre'] ?? null,
                'superficie_totale'        => $data['superficie_totale'] ?? null,
                'type_culture'             => $data['type_culture'] ?? null,
                'experience_annees'        => $data['experience_annees'] ?? null,
                'nombre_animaux'           => $data['nombre_animaux'] ?? null,
                'type_elevage'             => $data['type_elevage'] ?? null,
            ]);

            // Pré-création des 3 entrées de validation (en_attente)
            foreach ([
                AdhesionRequestValidation::LEVEL_PRESIDENT,
                AdhesionRequestValidation::LEVEL_SUPERVISEUR,
                AdhesionRequestValidation::LEVEL_ADMIN,
            ] as $level) {
                $request->validations()->create([
                    'level'    => $level,
                    'decision' => AdhesionRequestValidation::DECISION_PENDING,
                ]);
            }

            return $request;
        });
    }

    /**
     * Enregistre une décision pour un niveau donné. Met à jour `etape_courante`.
     * Aucun saut autorisé : président → superviseur → admin.
     */
    public function recordValidation(AdhesionRequest $request, User $validator, string $level, string $decision, ?string $motif = null): AdhesionRequest
    {
        $this->ensureLevelAllowed($request, $level, $validator);

        return DB::transaction(function () use ($request, $validator, $level, $decision, $motif) {
            $validation = $request->validations()->where('level', $level)->firstOrFail();

            if ($validation->decision !== AdhesionRequestValidation::DECISION_PENDING) {
                throw new BusinessException("Ce niveau a déjà été statué.", 422);
            }

            $validation->update([
                'decision'     => $decision,
                'validator_id' => $validator->id,
                'motif'        => $motif,
                'decided_at'   => now(),
            ]);

            $this->refreshRequestStage($request);

            return $request->fresh('validations.validator');
        });
    }

    private function ensureLevelAllowed(AdhesionRequest $request, string $level, User $user): void
    {
        $expected = match ($request->etape_courante) {
            AdhesionRequest::ETAPE_EN_ATTENTE         => AdhesionRequestValidation::LEVEL_PRESIDENT,
            AdhesionRequest::ETAPE_VALIDE_PRESIDENT   => AdhesionRequestValidation::LEVEL_SUPERVISEUR,
            AdhesionRequest::ETAPE_VALIDE_SUPERVISEUR => AdhesionRequestValidation::LEVEL_ADMIN,
            default => null,
        };

        if ($expected === null) {
            throw new BusinessException("Cette demande est déjà clôturée.", 422);
        }

        if ($level !== $expected) {
            throw new BusinessException("Le niveau attendu est : {$expected}.", 422);
        }

        $allowed = match ($level) {
            AdhesionRequestValidation::LEVEL_PRESIDENT   => $user->id === $request->union?->president_id,
            AdhesionRequestValidation::LEVEL_SUPERVISEUR => $user->isSupervisor() || $user->isAdminLike(),
            AdhesionRequestValidation::LEVEL_ADMIN       => $user->isAdminLike(),
        };

        if (! $allowed) {
            throw new BusinessException("Vous n'avez pas l'autorisation de valider à ce niveau.", 403);
        }
    }

    private function refreshRequestStage(AdhesionRequest $request): void
    {
        $byLevel = $request->validations()->get()->keyBy('level');

        $pres = $byLevel[AdhesionRequestValidation::LEVEL_PRESIDENT]   ?? null;
        $sup  = $byLevel[AdhesionRequestValidation::LEVEL_SUPERVISEUR] ?? null;
        $adm  = $byLevel[AdhesionRequestValidation::LEVEL_ADMIN]       ?? null;

        // Rejet à n'importe quel niveau → on clôture
        foreach ([$pres, $sup, $adm] as $v) {
            if ($v?->decision === AdhesionRequestValidation::DECISION_REJECTED) {
                $request->update([
                    'statut'         => 'rejete',
                    'etape_courante' => AdhesionRequest::ETAPE_REJETE,
                    'motif_rejet'    => $v->motif,
                ]);
                return;
            }
        }

        if ($adm?->decision === AdhesionRequestValidation::DECISION_APPROVED) {
            $request->update(['etape_courante' => AdhesionRequest::ETAPE_ACCEPTE]);
            return;
        }
        if ($sup?->decision === AdhesionRequestValidation::DECISION_APPROVED) {
            $request->update(['etape_courante' => AdhesionRequest::ETAPE_VALIDE_SUPERVISEUR]);
            return;
        }
        if ($pres?->decision === AdhesionRequestValidation::DECISION_APPROVED) {
            $request->update(['etape_courante' => AdhesionRequest::ETAPE_VALIDE_PRESIDENT]);
        }
    }

    /**
     * Active le membre une fois les 3 niveaux validés (Admin final).
     * Crée l'Adhesion + Cotisation initiale + numéro de membre unique par union.
     */
    public function activateMembership(AdhesionRequest $request, Client $client, int $adhesionTypeId, ?int $paymentModeId, string $processedBy): Adhesion
    {
        if ($request->etape_courante !== AdhesionRequest::ETAPE_ACCEPTE) {
            throw new BusinessException("La demande doit avoir reçu les 3 validations avant activation.", 422);
        }

        return DB::transaction(function () use ($request, $client, $adhesionTypeId, $paymentModeId, $processedBy) {
            $union  = Union::findOrFail($request->union_id);
            $status = AdhesionStatus::where('code', 'actif')->firstOrFail();

            $numero = $this->generateUniqueMembershipNumber($union);

            $adhesion = Adhesion::create([
                'id'                  => $this->idGenerator->generateAdhesionId(),
                'client_id'           => $client->id,
                'union_id'            => $union->id,
                'adhesion_type_id'    => $adhesionTypeId,
                'adhesion_status_id'  => $status->id,
                'numero_membre'       => $numero,
                'date_adhesion'       => now()->toDateString(),
                'prochaine_echeance'  => now()->addYear()->toDateString(),
                'cotisation_initiale' => $request->cotisation ?? 0,
                'cotisation_annuelle' => $request->cotisation ?? 0,
                'payment_mode_id'     => $paymentModeId,
            ]);

            Cotisation::create([
                'adhesion_id'     => $adhesion->id,
                'annee'           => now()->year,
                'montant'         => $adhesion->cotisation_initiale,
                'statut'          => 'en_attente',
                'payment_mode_id' => $paymentModeId,
            ]);

            $request->update([
                'statut'                 => 'approuve',
                'traite_par'             => $processedBy,
                'numero_membre_attribue' => $numero,
            ]);

            $union->increment('membres_total');

            $this->notificationService->create([
                'user_id'  => $client->id,
                'category' => 'app',
                'type'     => 'membership',
                'title'    => 'Adhésion activée',
                'body'     => "Bienvenue ! Votre numéro de membre est {$numero}.",
            ]);

            return $adhesion->load('client', 'union', 'status', 'type', 'paymentMode', 'cotisations');
        });
    }

    private function generateUniqueMembershipNumber(Union $union): string
    {
        // Le numéro doit être unique PAR union (cf. doc §6).
        do {
            $candidate = $this->idGenerator->generateMembershipNumber();
        } while (Adhesion::where('union_id', $union->id)
            ->where('numero_membre', $candidate)
            ->exists());

        return $candidate;
    }

    public function suspendAdhesion(Adhesion $adhesion): Adhesion
    {
        $status = AdhesionStatus::where('code', 'suspendu')->firstOrFail();
        $adhesion->update(['adhesion_status_id' => $status->id]);

        return $adhesion->fresh('status');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // RÉTRO-COMPATIBILITÉ (Api\AdhesionController existant)
    // À retirer une fois le controller migré vers le workflow 3 niveaux.
    // ─────────────────────────────────────────────────────────────────────────

    public function approveRequest(AdhesionRequest $request, Client $client, Union $union, int $adhesionTypeId, ?int $paymentModeId, string $processedBy): Adhesion
    {
        return $this->activateMembership($request, $client, $adhesionTypeId, $paymentModeId, $processedBy);
    }

    public function rejectRequest(AdhesionRequest $request, string $processedBy, ?string $motif = null): AdhesionRequest
    {
        if ($request->etape_courante === AdhesionRequest::ETAPE_REJETE) {
            throw new BusinessException('Cette demande est déjà rejetée.', 422);
        }

        $request->update([
            'statut'         => 'rejete',
            'etape_courante' => AdhesionRequest::ETAPE_REJETE,
            'motif_rejet'    => $motif,
            'traite_par'     => $processedBy,
        ]);

        return $request->fresh('treatedBy');
    }
}
