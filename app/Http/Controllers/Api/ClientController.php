<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreClientProfileRequest;
use App\Models\Client;
use App\Models\User;
use App\Services\ClientService;
use Illuminate\Http\JsonResponse;

class ClientController extends Controller
{
    public function index(): JsonResponse
    {
        $clients = Client::with(['user', 'activityType', 'structureType', 'superviseur'])
            ->latest()->paginate(15);

        return response()->json(['message' => 'Liste des clients.', 'data' => $clients]);
    }

    public function show(Client $client): JsonResponse
    {
        $client->load([
            'user', 'activityType', 'structureType', 'superviseur',
            'adhesions', 'credits', 'insurances', 'orders', 'reports', 'supportTickets',
        ]);

        return response()->json(['message' => 'Détail du client.', 'data' => $client]);
    }

    public function storeProfile(StoreClientProfileRequest $request, User $user, ClientService $clientService): JsonResponse
    {
        $client = $clientService->createClientProfile($user, $request->validated());

        return response()->json([
            'message' => 'Profil client enregistré avec succès.',
            'data'    => $client->load(['user', 'activityType', 'structureType', 'superviseur']),
        ], 201);
    }

    public function updateProfile(StoreClientProfileRequest $request, Client $client, ClientService $clientService): JsonResponse
    {
        $updated = $clientService->createClientProfile($client->user, $request->validated());

        return response()->json([
            'message' => 'Profil client mis à jour avec succès.',
            'data'    => $updated->load(['user', 'activityType', 'structureType', 'superviseur']),
        ]);
    }
}
