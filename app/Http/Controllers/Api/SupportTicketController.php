<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Support\AssignSupportTicketRequest;
use App\Http\Requests\Support\StoreSupportTicketRequest;
use App\Models\SupportTicket;
use App\Services\SupportTicketService;
use Illuminate\Http\JsonResponse;

class SupportTicketController extends Controller
{
    public function index(): JsonResponse
    {
        $items = SupportTicket::with(['client.user', 'category', 'treatedBy'])
            ->latest()->paginate(15);

        return response()->json(['message' => 'Liste des tickets de support.', 'data' => $items]);
    }

    public function store(StoreSupportTicketRequest $request, SupportTicketService $supportTicketService): JsonResponse
    {
        $ticket = $supportTicketService->createTicket($request->validated());

        return response()->json([
            'message' => 'Ticket créé avec succès.',
            'data'    => $ticket->load(['client.user', 'category']),
        ], 201);
    }

    public function show(SupportTicket $supportTicket): JsonResponse
    {
        $supportTicket->load(['client.user', 'category', 'treatedBy']);

        return response()->json(['message' => 'Détail du ticket.', 'data' => $supportTicket]);
    }

    public function assign(AssignSupportTicketRequest $request, SupportTicket $supportTicket, SupportTicketService $supportTicketService): JsonResponse
    {
        $ticket = $supportTicketService->assignTicket($supportTicket, $request->integer('agent_id'));

        return response()->json(['message' => 'Ticket assigné avec succès.', 'data' => $ticket]);
    }

    public function resolve(SupportTicket $supportTicket, SupportTicketService $supportTicketService): JsonResponse
    {
        return response()->json([
            'message' => 'Ticket résolu avec succès.',
            'data'    => $supportTicketService->resolveTicket($supportTicket),
        ]);
    }

    public function close(SupportTicket $supportTicket, SupportTicketService $supportTicketService): JsonResponse
    {
        return response()->json([
            'message' => 'Ticket fermé avec succès.',
            'data'    => $supportTicketService->closeTicket($supportTicket),
        ]);
    }
}
