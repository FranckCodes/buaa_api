<?php

namespace App\Services;

use App\Models\Reference\Role;
use App\Models\Reference\UserStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function createUser(array $data, array $roleCodes = []): User
    {
        return DB::transaction(function () use ($data, $roleCodes) {
            $status = UserStatus::where('code', $data['status_code'] ?? 'actif')->firstOrFail();

            $user = User::create([
                'nom_complet'    => $data['nom_complet'],
                'email'          => $data['email'],
                'telephone'      => $data['telephone'] ?? null,
                'password'       => Hash::make($data['password']),
                'user_status_id' => $status->id,
                'photo_profil'   => $data['photo_profil'] ?? null,
            ]);

            if (!empty($roleCodes)) {
                $roles = Role::whereIn('code', $roleCodes)->pluck('id')->all();
                $user->roles()->sync($roles);
            }

            return $user->load('roles', 'status');
        });
    }

    public function syncRoles(User $user, array $roleCodes): User
    {
        $roles = Role::whereIn('code', $roleCodes)->pluck('id')->all();
        $user->roles()->sync($roles);

        return $user->load('roles');
    }

    public function changeStatus(User $user, string $statusCode): User
    {
        $status = UserStatus::where('code', $statusCode)->firstOrFail();
        $user->update(['user_status_id' => $status->id]);

        return $user->fresh('status');
    }
}
