<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Adhesion\ApproveAdhesionRequestRequest;
use App\Http\Requests\Adhesion\DeactivateUnionRequest;
use App\Http\Requests\Adhesion\StoreAdhesionRequestRequest;
use App\Http\Requests\Adhesion\StoreUnionRequest;
use App\Http\Requests\Adhesion\ValidateAdhesionRequestRequest;
use App\Http\Resources\AdhesionRequestResource;
use App\Http\Resources\AdhesionResource;
use App\Http\Resources\UnionResource;
use App\Models\Adhesion;
use App\Models\AdhesionRequest;
use App\Models\Client;
use App\Models\Union;
use App\Services\AdhesionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdhesionController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // UNIONS
    // ─────────────────────────────────────────────────────────────────────────

    public function unionsIndex(): JsonResponse
    {
        return $this->paginatedResponse(
            Union::with(['status', 'president'])->latest()->paginate(15),
            'Liste des unions récupérée avec succès.',
            fn ($union) => new UnionResource($union)
        );
    }

    public function showUnion(Union $union): JsonResponse
    {
        $this->authorize('view', $union);

        return $this->successResponse(
            new UnionResource($union->load(['status', 'president', 'validator', 'deactivator', 'members', 'documents'])),
            "Détail de l'union récupéré avec succès."
        );
    }

    public function storeUnion(StoreUnionRequest $request, AdhesionService $adhesionService): JsonResponse
    {
        $this->authorize('create', Union::class);

        $union = $adhesionService->createUnion($request->validated());

        return $this->successResponse(
            new UnionResource($union->load(['status', 'president'])),
            'Union créée avec succès. Statut initial : SUSPENDU.',
            201
        );
    }

    /**
     * Le président soumet ses documents → l'union passe EN_REVUE.
     */
    public function submitUnionDocuments(Union $union, AdhesionService $adhesionService): JsonResponse
    {
        $this->authorize('submitDocuments', $union);

        $union = $adhesionService->submitUnionDocuments($union);

        return $this->successResponse(
            new UnionResource($union->load(['status', 'president'])),
            'Documents soumis. Union en attente de validation Admin.'
        );
    }

    /**
     * Validation finale par l'Admin → l'union devient ACTIVE.
     */
    public function activateUnion(Request $request, Union $union, AdhesionService $adhesionService): JsonResponse
    {
        $this->authorize('validate', $union);

        $union = $adhesionService->activateUnion($union, $request->user());

        return $this->successResponse(
            new UnionResource($union->load(['status', 'president', 'validator'])),
            'Union activée avec succès.'
        );
    }

    public function suspendUnion(Union $union, AdhesionService $adhesionService): JsonResponse
    {
        $this->authorize('suspend', $union);

        $union = $adhesionService->suspendUnion($union);

        return $this->successResponse(
            new UnionResource($union->load(['status'])),
            'Union suspendue.'
        );
    }

    /**
     * Désactivation définitive — Super Admin uniquement, irréversible.
     */
    public function deactivateUnion(DeactivateUnionRequest $request, Union $union, AdhesionService $adhesionService): JsonResponse
    {
        $this->authorize('deactivate', $union);

        $union = $adhesionService->deactivateUnion(
            $union,
            $request->user(),
            $request->validated()['reason']
        );

        return $this->successResponse(
            new UnionResource($union->load(['status', 'deactivator'])),
            'Union désactivée définitivement.'
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // ADHESION REQUESTS
    // ─────────────────────────────────────────────────────────────────────────

    public function requestsIndex(): JsonResponse
    {
        return $this->paginatedResponse(
            AdhesionRequest::with(['union', 'activityType', 'structureType', 'treatedBy', 'validations.validator'])
                ->latest()->paginate(15),
            "Liste des demandes d'adhésion récupérée avec succès.",
            fn ($item) => new AdhesionRequestResource($item)
        );
    }

    public function showRequest(AdhesionRequest $adhesionRequest): JsonResponse
    {
        return $this->successResponse(
            new AdhesionRequestResource($adhesionRequest->load([
                'union', 'activityType', 'structureType', 'treatedBy',
                'validations.validator', 'documents',
            ])),
            "Détail de la demande récupéré avec succès."
        );
    }

    public function storeRequest(StoreAdhesionRequestRequest $request, AdhesionService $adhesionService): JsonResponse
    {
        $this->authorize('createRequest', Adhesion::class);

        $item = $adhesionService->createRequest($request->validated());

        return $this->successResponse(
            new AdhesionRequestResource($item->load(['union', 'validations'])),
            "Demande d'adhésion créée avec succès.",
            201
        );
    }

    /**
     * Workflow 3 niveaux : président → superviseur → admin.
     * Body : { level: "president"|"superviseur"|"admin", decision: "valide"|"rejete", motif?: string }
     */
    public function validateRequest(ValidateAdhesionRequestRequest $request, AdhesionRequest $adhesionRequest, AdhesionService $adhesionService): JsonResponse
    {
        $data = $request->validated();

        $item = $adhesionService->recordValidation(
            $adhesionRequest,
            $request->user(),
            $data['level'],
            $data['decision'],
            $data['motif'] ?? null
        );

        return $this->successResponse(
            new AdhesionRequestResource($item->load(['union', 'validations.validator'])),
            "Validation enregistrée."
        );
    }

    /**
     * Activation finale du membre (après les 3 validations).
     * Crée l'Adhesion + cotisation initiale.
     */
    public function activateMembership(ApproveAdhesionRequestRequest $request, AdhesionRequest $adhesionRequest, AdhesionService $adhesionService): JsonResponse
    {
        $this->authorize('approve', Adhesion::class);

        $data = $request->validated();

        $adhesion = $adhesionService->activateMembership(
            $adhesionRequest,
            Client::findOrFail($data['client_id']),
            $data['adhesion_type_id'],
            $data['payment_mode_id'] ?? null,
            $data['processed_by']
        );

        return $this->successResponse(
            new AdhesionResource($adhesion),
            "Membre activé avec succès."
        );
    }

    // ── LEGACY (rétro-compat) ────────────────────────────────────────────────

    public function approveRequest(ApproveAdhesionRequestRequest $request, AdhesionRequest $adhesionRequest, AdhesionService $adhesionService): JsonResponse
    {
        return $this->activateMembership($request, $adhesionRequest, $adhesionService);
    }

    public function rejectRequest(Request $request, AdhesionRequest $adhesionRequest, AdhesionService $adhesionService): JsonResponse
    {
        $this->authorize('approve', Adhesion::class);
        $request->validate([
            'processed_by' => ['required', 'string', 'exists:users,id'],
            'motif'        => ['nullable', 'string'],
        ]);

        $item = $adhesionService->rejectRequest(
            $adhesionRequest,
            $request->string('processed_by')->toString(),
            $request->input('motif'),
        );

        return $this->successResponse(new AdhesionRequestResource($item), "Demande d'adhésion rejetée avec succès.");
    }

    // ─────────────────────────────────────────────────────────────────────────
    // ADHESIONS
    // ─────────────────────────────────────────────────────────────────────────

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Adhesion::class);

        return $this->paginatedResponse(
            Adhesion::with(['client.user', 'union', 'type', 'status', 'paymentMode'])->latest()->paginate(15),
            'Liste des adhésions récupérée avec succès.',
            fn ($adhesion) => new AdhesionResource($adhesion)
        );
    }

    public function show(Adhesion $adhesion): JsonResponse
    {
        $this->authorize('view', $adhesion);

        return $this->successResponse(
            new AdhesionResource($adhesion->load(['client.user', 'union', 'type', 'status', 'paymentMode', 'cotisations.paymentMode', 'documents'])),
            "Détail de l'adhésion récupéré avec succès."
        );
    }
}
