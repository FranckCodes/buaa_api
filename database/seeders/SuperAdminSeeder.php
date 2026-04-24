<?php

namespace Database\Seeders;

use App\Models\Reference\Role;
use App\Models\Reference\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $status = UserStatus::where('code', 'actif')->firstOrFail();

        $user = User::updateOrCreate(
            ['email' => 'superadmin@buaa.cd'],
            [
                'id'             => 'ADM-001',
                'nom_complet'    => 'Super Administrateur',
                'login_code'     => 'SUPA9K3R2026',
                'telephone'      => '+243827029543',
                'password'       => Hash::make('password'),
                'user_status_id' => $status->id,
            ]
        );

        $role = Role::where('code', 'super_admin')->firstOrFail();
        $user->roles()->syncWithoutDetaching([$role->id]);
    }
}
