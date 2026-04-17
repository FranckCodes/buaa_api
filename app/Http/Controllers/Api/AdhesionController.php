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
        return AdhesionRequestResource::collection(
            AdhesionRequest::with(['activityType', 'structureType', 'treatedBy'])->latest()->paginate(15)
        )->additional(["message" => "Liste des demandes d'adhésion."])->response();
    }

    public function storeRequest(StoreAdhesionRequestRequest $request, AdhesionService $adhesionService): JsonResponse
    {
        $item = $adhesionService->createRequest($request->validated());

        return response()->json([
            "message" => "Demande d'adhésion créée avec succès.",
            'data'    => new AdhesionRequestResource($item),
        ], 201);
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

        return response()->json([
            "message" => "Demande d'adhésion approuvée avec succès.",
            'data'    => new AdhesionResource($adhesion),
        ]);
    }

    public function rejectRequest(Request $request, AdhesionRequest $adhesionRequest, AdhesionService $adhesionService): JsonResponse
    {
        $request->validate(['processed_by' => ['required', 'integer', 'exists:users,id']]);
        $item = $adhesionService->rejectRequest($adhesionRequest, $request->integer('processed_by'));

        return response()->json(["message" => "Demande d'adhésion rejetée.", 'data' => new AdhesionRequestResource($item)]);
    }

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Adhesion::class);

        return AdhesionResource::collection(
            Adhesion::with(['client.user', 'union', 'type', 'status', 'paymentMode'])->latest()->paginate(15)
        )->additional(['message' => 'Liste des adhésions.'])->response();
    }

    public function show(Adhesion $adhesion): JsonResponse
    {
        $this->authorize('view', $adhesion);

        return response()->json([
            "message" => "Détail de l'adhésion.",
            'data'    => new AdhesionResource($adhesion->load(['client.user', 'union', 'type', 'status', 'paymentMode', 'cotisations', 'documents'])),
        ]);
    }
}
