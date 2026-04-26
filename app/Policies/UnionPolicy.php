<?php

namespace App\Policies;

use App\Models\Union;
use App\Models\User;

class UnionPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isSuperAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Union $union): bool
    {
        if ($user->isStaff()) return true;
        if ($user->id === $union->president_id) return true;
        return $union->isActive();
    }

    public function create(User $user): bool
    {
        return $user->isAdminLike();
    }

    public function update(User $user, Union $union): bool
    {
        if ($user->isAdminLike()) return true;
        return $user->id === $union->president_id;
    }

    public function submitDocuments(User $user, Union $union): bool
    {
        return $user->id === $union->president_id;
    }

    public function validate(User $user, Union $union): bool
    {
        return $user->isAdminLike();
    }

    public function suspend(User $user, Union $union): bool
    {
        return $user->isAdminLike();
    }

    /**
     * Désactivation définitive : Super Admin uniquement.
     * Le `before()` couvre déjà ce cas — cette méthode renvoie false pour tout autre rôle.
     */
    public function deactivate(User $user, Union $union): bool
    {
        return false;
    }
}
