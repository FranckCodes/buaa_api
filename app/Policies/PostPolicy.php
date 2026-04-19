<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isSuperAdmin() ? true : null;
    }

    public function viewAny(?User $user = null): bool { return true; }
    public function view(?User $user, Post $post): bool { return true; }
    public function create(User $user): bool { return $user->isClient() || $user->isStaff(); }

    public function update(User $user, Post $post): bool
    {
        if ($user->isAdminLike()) return true;
        return $post->author_id === $user->id;
    }

    public function delete(User $user, Post $post): bool
    {
        if ($user->isAdminLike()) return true;
        return $post->author_id === $user->id;
    }

    public function moderate(User $user): bool { return $user->isAdminLike() || $user->isSupervisor(); }
    public function interact(User $user): bool { return $user->isClient() || $user->isStaff(); }
}
