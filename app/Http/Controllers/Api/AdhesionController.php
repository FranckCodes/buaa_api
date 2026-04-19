<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Adhesion\ApproveAdhesionRequestRequest;
use App\Http\Requests\Adhesion\StoreAdhesionRequestRequest;
use App\Http\Requests\Adhesion\StoreUnionRequest;
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
    public function unionsIndex(): JsonResponse
    {
        return $this->paginatedResponse(
            Union::latest()->paginate(15),
            'Liste des unions récupérée avec succès.',
            fn ($union) => new UnionResource($union)
        );
    }

    public function storeUnion(StoreUnionRequest $request, AdhesionService $adhesionService): JsonResponse
    {
        $union = $adhesionService->createUnion($request->validated());

        return $this->successResponse(new UnionResource($union), 'Union créée avec succès.', 201);
    }

    public function requestsIndex(): JsonResponse
    {
        return $this->paginatedResponse(
            AdhesionRequest::with(['activityType', 'structureType', 'treatedBy'])->latest()->paginate(15),
            "Liste des demandes d'adhésion récupérée avec succès.",
            fn ($item) => new AdhesionRequestResource($item)
        );
    }

    public function storeRequest(StoreAdhesionRequestRequest $request, AdhesionService $adhesionService): JsonResponse
    {
        $this->authorize('createRequest', Adhesion::class);

        $item = $adhesionService->createRequest($request->validated());

        return $this->successResponse(new AdhesionRequestResource($item), "Demande d'adhésion créée avec succès.", 201);
    }

    public function approveRequest(ApproveAdhesionRequestRequest $request, AdhesionRequest $adhesionRequest, AdhesionService $adhesionService): JsonResponse
    {
        $this->authorize('approve', Adhesion::class);

        $data     = $request->validated();
        $adhesion = $adhesionService->approveRequest(
            $adhesionRequest,
            Client::findOrFail($data['client_id']),
            Union::findOrFail($data['union_id']),
            $data['adhesion_type_id'],
            $data['payment_mode_id'] ?? null,
            $data['processed_by']
        );

        return $this->successResponse(new AdhesionResource($adhesion), "Demande d'adhésion approuvée avec succès.");
    }

    public function rejectRequest(Request $request, AdhesionRequest $adhesionRequest, AdhesionService $adhesionService): JsonResponse
    {
        $this->authorize('approve', Adhesion::class);
        $request->validate(['processed_by' => ['required', 'string', 'exists:users,id']]);

        $item = $adhesionService->rejectRequest($adhesionRequest, $request->string('processed_by')->toString());

        return $this->successResponse(new AdhesionRequestResource($item), "Demande d'adhésion rejetée avec succès.");
    }

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
