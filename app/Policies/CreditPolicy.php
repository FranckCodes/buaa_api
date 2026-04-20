<?php

namespace App\Policies;

use App\Models\Credit;
use App\Models\User;

class CreditPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isSuperAdmin() ? true : null;
    }

    public function viewAny(User $user): bool { return $user->isStaff(); }

    public function view(User $user, Credit $credit): bool
    {
        if ($user->isAdminLike()) return true;
        if ($user->isSupervisor() && $credit->client?->superviseur_id === $user->id) return true;
        return $user->id === $credit->client_id;
    }

    public function create(User $user): bool { return $user->isClient() || $user->isAdminLike(); }
    public function approve(User $user): bool { return $user->isAdminLike(); }
    public function reject(User $user): bool { return $user->isAdminLike(); }
    public function registerPayment(User $user): bool { return $user->isAdminLike(); }
}
