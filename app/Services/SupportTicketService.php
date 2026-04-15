<?php

namespace App\Services;

use App\Models\SupportTicket;

class SupportTicketService
{
    public function createTicket(array $data): SupportTicket
    {
        return SupportTicket::create([
            'client_id'           => $data['client_id'],
            'support_category_id' => $data['support_category_id'],
            'sujet'               => $data['sujet'],
            'description'         => $data['description'] ?? null,
            'statut'              => 'ouvert',
        ]);
    }

    public function assignTicket(SupportTicket $ticket, int $agentId): SupportTicket
    {
        $ticket->update(['traite_par' => $agentId, 'statut' => 'en_cours']);

        return $ticket->fresh();
    }

    public function resolveTicket(SupportTicket $ticket): SupportTicket
    {
        $ticket->update(['statut' => 'resolu', 'resolved_at' => now()]);

        return $ticket->fresh();
    }

    public function closeTicket(SupportTicket $ticket): SupportTicket
    {
        $ticket->update(['statut' => 'ferme']);

        return $ticket->fresh();
    }
}
