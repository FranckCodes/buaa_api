<?php

namespace Tests\Concerns;

use App\Models\Reference\Role;
use App\Models\User;

trait InteractsWithRoles
{
    protected function assignRole(User $user, string $roleCode): User
    {
        $role = Role::where('code', $roleCode)->firstOrFail();
        $user->roles()->syncWithoutDetaching([$role->id]);

        return $user->fresh('roles');
    }

    protected function createUserWithRole(string $roleCode): User
    {
        $user = User::factory()->create();
        return $this->assignRole($user, $roleCode);
    }
}
