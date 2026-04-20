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

        return $this->paginatedResponse(
            SupportTicket::with(['client.user', 'category', 'treatedBy'])->latest()->paginate(15),
            'Liste des tickets de support récupérée avec succès.',
            fn ($ticket) => new SupportTicketResource($ticket)
        );
    }

    public function store(StoreSupportTicketRequest $request, SupportTicketService $supportTicketService): JsonResponse
    {
        $this->authorize('create', SupportTicket::class);

        $ticket = $supportTicketService->createTicket($request->validated());

        return $this->successResponse(
            new SupportTicketResource($ticket->load(['client.user', 'category', 'treatedBy'])),
            'Ticket créé avec succès.',
            201
        );
    }

    public function show(SupportTicket $supportTicket): JsonResponse
    {
        $this->authorize('view', $supportTicket);

        return $this->successResponse(
            new SupportTicketResource($supportTicket->load(['client.user', 'category', 'treatedBy'])),
            'Détail du ticket récupéré avec succès.'
        );
    }

    public function assign(AssignSupportTicketRequest $request, SupportTicket $supportTicket, SupportTicketService $supportTicketService): JsonResponse
    {
        $this->authorize('assign', $supportTicket);

        return $this->successResponse(
            new SupportTicketResource($supportTicketService->assignTicket($supportTicket, $request->string('agent_id')->toString())),
            'Ticket assigné avec succès.'
        );
    }

    public function resolve(SupportTicket $supportTicket, SupportTicketService $supportTicketService): JsonResponse
    {
        $this->authorize('resolve', $supportTicket);

        return $this->successResponse(
            new SupportTicketResource($supportTicketService->resolveTicket($supportTicket)),
            'Ticket résolu avec succès.'
        );
    }

    public function close(SupportTicket $supportTicket, SupportTicketService $supportTicketService): JsonResponse
    {
        $this->authorize('close', $supportTicket);

        return $this->successResponse(
            new SupportTicketResource($supportTicketService->closeTicket($supportTicket)),
            'Ticket fermé avec succès.'
        );
    }
}
