<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Adhesion\ApproveAdhesionRequestRequest;
use App\Http\Requests\Adhesion\StoreAdhesionRequestRequest;
use App\Http\Resources\AdhesionRequestResource;
use App\Http\Resources\AdhesionResource;
use App\Models\Adhesion;
use App\Models\AdhesionRequest;
use App\Models\Client;
use App\Models\Union;
use App\Services\AdhesionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdhesionController extends Controller
{
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

        return $this->successResponse(
            new AdhesionRequestResource($adhesionService->createRequest($request->validated())),
            "Demande d'adhésion créée avec succès.",
            201
        );
    }

    public function approveRequest(ApproveAdhesionRequestRequest $request, AdhesionRequest $adhesionRequest, AdhesionService $adhesionService): JsonResponse
    {
        $this->authorize('approve', Adhesion::class);
        $data = $request->validated();

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
        $request->validate(['processed_by' => ['required', 'integer', 'exists:users,id']]);

        return $this->successResponse(
            new AdhesionRequestResource($adhesionService->rejectRequest($adhesionRequest, $request->integer('processed_by'))),
            "Demande d'adhésion rejetée."
        );
    }

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Adhesion::class);

        return $this->paginatedResponse(
            Adhesion::with(['client.user', 'union', 'type', 'status', 'paymentMode'])->latest()->paginate(15),
            'Liste des adhésions récupérée avec succès.',
            fn ($item) => new AdhesionResource($item)
        );
    }

    public function show(Adhesion $adhesion): JsonResponse
    {
        $this->authorize('view', $adhesion);

        return $this->successResponse(
            new AdhesionResource($adhesion->load(['client.user', 'union', 'type', 'status', 'paymentMode', 'cotisations', 'documents'])),
            "Détail de l'adhésion récupéré avec succès."
        );
    }
}
