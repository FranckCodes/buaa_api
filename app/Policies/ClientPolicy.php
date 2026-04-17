<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isSuperAdmin() ? true : null;
    }

    public function viewAny(User $user): bool { return $user->isStaff(); }

    public function view(User $user, Client $client): bool
    {
        if ($user->isAdminLike()) return true;
        if ($user->isSupervisor() && $client->superviseur_id === $user->id) return true;
        return $user->id === $client->user_id;
    }

    public function create(User $user): bool { return $user->isAdminLike(); }

    public function update(User $user, Client $client): bool
    {
        if ($user->isAdminLike()) return true;
        return $user->id === $client->user_id;
    }

    public function assignSupervisor(User $user): bool { return $user->isAdminLike(); }
}
