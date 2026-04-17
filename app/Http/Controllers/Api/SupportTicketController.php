<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Support\AssignSupportTicketRequest;
use App\Http\Requests\Support\StoreSupportTicketRequest;
use App\Http\Resources\SupportTicketResource;
use App\Models\SupportTicket;
use App\Services\SupportTicketService;
use Illuminate\Http\JsonResponse;

class SupportTicketController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', SupportTicket::class);

        return SupportTicketResource::collection(
            SupportTicket::with(['client.user', 'category', 'treatedBy'])->latest()->paginate(15)
        )->additional(['message' => 'Liste des tickets de support.'])->response();
    }

    public function store(StoreSupportTicketRequest $request, SupportTicketService $supportTicketService): JsonResponse
    {
        $this->authorize('create', SupportTicket::class);
        $ticket = $supportTicketService->createTicket($request->validated());

        return response()->json([
            'message' => 'Ticket créé avec succès.',
            'data'    => new SupportTicketResource($ticket->load(['client.user', 'category'])),
        ], 201);
    }

    public function show(SupportTicket $supportTicket): JsonResponse
    {
        $this->authorize('view', $supportTicket);

        return response()->json([
            'message' => 'Détail du ticket.',
            'data'    => new SupportTicketResource($supportTicket->load(['client.user', 'category', 'treatedBy'])),
        ]);
    }

    public function assign(AssignSupportTicketRequest $request, SupportTicket $supportTicket, SupportTicketService $supportTicketService): JsonResponse
    {
        $this->authorize('assign', $supportTicket);
        $ticket = $supportTicketService->assignTicket($supportTicket, $request->integer('agent_id'));

        return response()->json(['message' => 'Ticket assigné avec succès.', 'data' => new SupportTicketResource($ticket)]);
    }

    public function resolve(SupportTicket $supportTicket, SupportTicketService $supportTicketService): JsonResponse
    {
        $this->authorize('resolve', $supportTicket);

        return response()->json([
            'message' => 'Ticket résolu avec succès.',
            'data'    => new SupportTicketResource($supportTicketService->resolveTicket($supportTicket)),
        ]);
    }

    public function close(SupportTicket $supportTicket, SupportTicketService $supportTicketService): JsonResponse
    {
        $this->authorize('close', $supportTicket);

        return response()->json([
            'message' => 'Ticket fermé avec succès.',
            'data'    => new SupportTicketResource($supportTicketService->closeTicket($supportTicket)),
        ]);
    }
}
