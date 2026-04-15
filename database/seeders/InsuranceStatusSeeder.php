<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InsuranceStatusSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('insurance_statuses')->upsert([
            ['code' => 'active', 'label' => 'Active', 'description' => "Police d'assurance active.", 'is_active' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'en_attente', 'label' => 'En attente', 'description' => 'Souscription en attente de validation.', 'is_active' => true, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'expiree', 'label' => 'Expirée', 'description' => "Police arrivée à expiration.", 'is_active' => true, 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'suspendue', 'label' => 'Suspendue', 'description' => 'Police suspendue.', 'is_active' => true, 'sort_order' => 4, 'created_at' => $now, 'updated_at' => $now],
        ], ['code'], ['label', 'description', 'is_active', 'sort_order', 'updated_at']);
    }
}
