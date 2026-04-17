<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isSuperAdmin() ? true : null;
    }

    public function view(User $user, Document $document): bool
    {
        $parent = $document->documentable;

        if (!$parent) return false;
        if ($user->isAdminLike()) return true;

        if ($parent instanceof Client) {
            return $parent->user_id === $user->id;
        }

        if (isset($parent->client_id)) {
            if ($parent->client?->user_id === $user->id) return true;
            if ($parent->client?->superviseur_id === $user->id) return true;
        }

        return false;
    }

    public function create(User $user): bool { return $user->isClient() || $user->isStaff(); }
    public function delete(User $user): bool { return $user->isAdminLike(); }
}
