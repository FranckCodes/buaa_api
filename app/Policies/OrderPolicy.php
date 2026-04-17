<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isSuperAdmin() ? true : null;
    }

    public function viewAny(User $user): bool { return $user->isStaff(); }

    public function view(User $user, Order $order): bool
    {
        if ($user->isAdminLike()) return true;
        if ($user->isSupervisor() && $order->client?->superviseur_id === $user->id) return true;
        return $user->id === $order->client?->user_id;
    }

    public function create(User $user): bool { return $user->isClient() || $user->isAdminLike(); }
    public function approve(User $user): bool { return $user->isAdminLike() || $user->isSupervisor(); }
    public function reject(User $user): bool { return $user->isAdminLike() || $user->isSupervisor(); }
    public function deliver(User $user): bool { return $user->isAdminLike(); }
}
