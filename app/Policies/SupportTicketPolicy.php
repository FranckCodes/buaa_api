<?php

namespace App\Policies;

use App\Models\SupportTicket;
use App\Models\User;

class SupportTicketPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isSuperAdmin() ? true : null;
    }

    public function viewAny(User $user): bool { return $user->isStaff(); }

    public function view(User $user, SupportTicket $ticket): bool
    {
        if ($user->isAdminLike()) return true;
        if ($user->isSupervisor() && $ticket->client?->superviseur_id === $user->id) return true;
        return $user->id === $ticket->client?->user_id;
    }

    public function create(User $user): bool { return $user->isClient() || $user->isAdminLike(); }
    public function assign(User $user): bool { return $user->isAdminLike() || $user->isSupervisor(); }
    public function resolve(User $user): bool { return $user->isAdminLike() || $user->isSupervisor(); }
    public function close(User $user): bool { return $user->isAdminLike(); }
}
