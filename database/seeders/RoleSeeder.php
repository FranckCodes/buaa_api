<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('roles')->upsert([
            ['code' => 'super_admin', 'label' => 'Super administrateur', 'description' => 'Accès total à toute la plateforme.', 'is_active' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'admin', 'label' => 'Administrateur', 'description' => 'Gestion administrative des modules et opérations.', 'is_active' => true, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'superviseur', 'label' => 'Superviseur', 'description' => 'Suivi terrain, validation et accompagnement des clients.', 'is_active' => true, 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'client', 'label' => 'Client', 'description' => 'Utilisateur bénéficiaire des services BUAA.', 'is_active' => true, 'sort_order' => 4, 'created_at' => $now, 'updated_at' => $now],
        ], ['code'], ['label', 'description', 'is_active', 'sort_order', 'updated_at']);
    }
}
