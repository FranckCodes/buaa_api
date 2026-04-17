<?php

namespace App\Policies;

use App\Models\Adhesion;
use App\Models\User;

class AdhesionPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isSuperAdmin() ? true : null;
    }

    public function viewAny(User $user): bool { return $user->isStaff(); }

    public function view(User $user, Adhesion $adhesion): bool
    {
        if ($user->isAdminLike()) return true;
        if ($user->isSupervisor() && $adhesion->client?->superviseur_id === $user->id) return true;
        return $user->id === $adhesion->client?->user_id;
    }

    public function createRequest(User $user): bool { return $user->isClient() || $user->isAdminLike(); }
    public function approve(User $user): bool { return $user->isAdminLike() || $user->isSupervisor(); }
    public function suspend(User $user): bool { return $user->isAdminLike(); }
}
