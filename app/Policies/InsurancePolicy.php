<?php

namespace App\Policies;

use App\Models\Insurance;
use App\Models\User;

class InsurancePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isSuperAdmin() ? true : null;
    }

    public function viewAny(User $user): bool { return $user->isStaff(); }

    public function view(User $user, Insurance $insurance): bool
    {
        if ($user->isAdminLike()) return true;
        if ($user->isSupervisor() && $insurance->client?->superviseur_id === $user->id) return true;
        return $user->id === $insurance->client?->user_id;
    }

    public function create(User $user): bool { return $user->isClient() || $user->isAdminLike(); }
    public function activate(User $user): bool { return $user->isAdminLike(); }
    public function manageClaims(User $user): bool { return $user->isAdminLike(); }
}
