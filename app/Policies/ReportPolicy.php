<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;

class ReportPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isSuperAdmin() ? true : null;
    }

    public function viewAny(User $user): bool { return $user->isStaff(); }

    public function view(User $user, Report $report): bool
    {
        if ($user->isAdminLike()) return true;
        if ($user->isSupervisor() && $report->superviseur_id === $user->id) return true;
        return $user->id === $report->client_id;
    }

    public function create(User $user): bool { return $user->isClient() || $user->isAdminLike(); }

    public function moderate(User $user, Report $report): bool
    {
        if ($user->isAdminLike()) return true;
        return $user->isSupervisor() && $report->superviseur_id === $user->id;
    }
}
