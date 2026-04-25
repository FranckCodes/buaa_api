<?php

namespace App\Services;

use App\Models\Reference\Role;
use App\Models\Reference\UserStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(protected IdGeneratorService $idGenerator) {}

    protected function resolveUserIdByRole(array $roleCodes): string
    {
        $prefix = match (true) {
            in_array('super_admin', $roleCodes) => 'ADM',
            in_array('admin', $roleCodes)       => 'ADM',
            in_array('superviseur', $roleCodes) => 'SUP',
            in_array('client', $roleCodes)      => 'CLT',
            default                             => 'USR',
        };

        return $prefix . '-' . str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT);
    }

    /**
     * Génère un login_code unique basé sur le rôle.
     * Format : PREFIX-XXXXXXYYYY (6 chars aléatoires + année)
     * Exemples : CLT-A3K9P22026, SUP-X7M2Q2026, ADM-B5R1T2026
     */
    protected function generateLoginCode(array $roleCodes): string
    {
        $prefix = match (true) {
            in_array('super_admin', $roleCodes) => 'ADM',
            in_array('admin', $roleCodes)       => 'ADM',
            in_array('superviseur', $roleCodes) => 'SUP',
            in_array('client', $roleCodes)      => 'CLT',
            default                             => 'USR',
        };

        $year   = date('Y');
        $chars  = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $random = '';

        for ($i = 0; $i < 6; $i++) {
            $random .= $chars[random_int(0, strlen($chars) - 1)];
        }

        $code = $prefix . '-' . $random . $year;

        // Garantir l'unicité
        if (User::where('login_code', $code)->exists()) {
            return $this->generateLoginCode($roleCodes);
        }

        return $code;
    }

    public function createUser(array $data, array $roleCodes = []): User
    {
        return DB::transaction(function () use ($data, $roleCodes) {
            $status = UserStatus::where('code', $data['status_code'] ?? 'actif')->firstOrFail();

            $user = User::create([
                'id'             => $data['id'] ?? $this->resolveUserIdByRole($roleCodes),
                'nom'            => $data['nom'],
                'postnom'        => $data['postnom'] ?? null,
                'prenom'         => $data['prenom'],
                'email'          => $data['email'],
                'login_code'     => $data['login_code'] ?? $this->generateLoginCode($roleCodes),
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
