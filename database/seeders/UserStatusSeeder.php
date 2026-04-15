<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserStatusSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('user_statuses')->upsert([
            ['code' => 'actif', 'label' => 'Actif', 'description' => 'Compte actif et utilisable.', 'is_active' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'suspendu', 'label' => 'Suspendu', 'description' => 'Compte temporairement suspendu.', 'is_active' => true, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'inactif', 'label' => 'Inactif', 'description' => 'Compte non actif ou désactivé.', 'is_active' => true, 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
        ], ['code'], ['label', 'description', 'is_active', 'sort_order', 'updated_at']);
    }
}
