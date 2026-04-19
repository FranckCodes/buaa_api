<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\SupportTicket;

class SupportTicketService
{
    public function __construct(
        protected IdGeneratorService $idGenerator,
        protected NotificationService $notificationService
    ) {}

    public function createTicket(array $data): SupportTicket
    {
        return SupportTicket::create([
            'id'                  => $this->idGenerator->generateSupportTicketId(),
            'client_id'           => $data['client_id'],
            'support_category_id' => $data['support_category_id'],
            'sujet'               => $data['sujet'],
            'description'         => $data['description'] ?? null,
            'statut'              => 'ouvert',
        ]);
    }

    public function assignTicket(SupportTicket $ticket, string $agentId): SupportTicket
    {
        if ($ticket->statut === 'ferme') {
            throw new BusinessException('Un ticket fermé ne peut pas être assigné.', 422);
        }

        $ticket->update(['traite_par' => $agentId, 'statut' => 'en_cours']);

        return $ticket->fresh(['category', 'client', 'treatedBy']);
    }

    public function resolveTicket(SupportTicket $ticket): SupportTicket
    {
        if ($ticket->statut === 'ferme') {
            throw new BusinessException('Un ticket fermé ne peut pas être résolu.', 422);
        }

        $ticket->update(['statut' => 'resolu', 'resolved_at' => now()]);

        $this->notificationService->create([
            'user_id'  => $ticket->client_id,
            'category' => 'app',
            'type'     => 'success',
            'title'    => 'Ticket résolu',
            'body'     => 'Votre ticket de support a été résolu.',
        ]);

        return $ticket->fresh(['category', 'client', 'treatedBy']);
    }

    public function closeTicket(SupportTicket $ticket): SupportTicket
    {
        if ($ticket->statut === 'ferme') {
            throw new BusinessException('Ce ticket est déjà fermé.', 422);
        }

        $ticket->update(['statut' => 'ferme']);

        return $ticket->fresh(['category', 'client', 'treatedBy']);
    }
}
