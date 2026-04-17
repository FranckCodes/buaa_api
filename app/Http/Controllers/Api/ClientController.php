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

        return $this->paginatedResponse(
            Client::with(['user', 'activityType', 'structureType', 'superviseur'])->latest()->paginate(15),
            'Liste des clients récupérée avec succès.',
            fn ($client) => new ClientResource($client)
        );
    }

    public function show(Client $client): JsonResponse
    {
        $this->authorize('view', $client);

        return $this->successResponse(
            new ClientResource($client->load(['user', 'activityType', 'structureType', 'superviseur', 'adhesions', 'credits', 'insurances', 'orders', 'reports', 'supportTickets'])),
            'Détail du client récupéré avec succès.'
        );
    }

    public function storeProfile(StoreClientProfileRequest $request, User $user, ClientService $clientService): JsonResponse
    {
        $client = $clientService->createClientProfile($user, $request->validated());

        return $this->successResponse(
            new ClientResource($client->load(['user', 'activityType', 'structureType', 'superviseur'])),
            'Profil client enregistré avec succès.',
            201
        );
    }

    public function updateProfile(StoreClientProfileRequest $request, Client $client, ClientService $clientService): JsonResponse
    {
        $this->authorize('update', $client);
        $updated = $clientService->createClientProfile($client->user, $request->validated());

        return $this->successResponse(
            new ClientResource($updated->load(['user', 'activityType', 'structureType', 'superviseur'])),
            'Profil client mis à jour avec succès.'
        );
    }
}
