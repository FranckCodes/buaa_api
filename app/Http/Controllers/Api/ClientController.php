<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreClientProfileRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Models\User;
use App\Services\ClientService;
use Illuminate\Http\JsonResponse;

class ClientController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Client::class);

        return ClientResource::collection(
            Client::with(['user', 'activityType', 'structureType', 'superviseur'])->latest()->paginate(15)
        )->additional(['message' => 'Liste des clients.'])->response();
    }

    public function show(Client $client): JsonResponse
    {
        $this->authorize('view', $client);

        return response()->json([
            'message' => 'Détail du client.',
            'data'    => new ClientResource($client->load([
                'user', 'activityType', 'structureType', 'superviseur',
                'adhesions', 'credits', 'insurances', 'orders', 'reports', 'supportTickets',
            ])),
        ]);
    }

    public function storeProfile(StoreClientProfileRequest $request, User $user, ClientService $clientService): JsonResponse
    {
        $client = $clientService->createClientProfile($user, $request->validated());

        return response()->json([
            'message' => 'Profil client enregistré avec succès.',
            'data'    => new ClientResource($client->load(['user', 'activityType', 'structureType', 'superviseur'])),
        ], 201);
    }

    public function updateProfile(StoreClientProfileRequest $request, Client $client, ClientService $clientService): JsonResponse
    {
        $this->authorize('update', $client);
        $updated = $clientService->createClientProfile($client->user, $request->validated());

        return response()->json([
            'message' => 'Profil client mis à jour avec succès.',
            'data'    => new ClientResource($updated->load(['user', 'activityType', 'structureType', 'superviseur'])),
        ]);
    }
}
