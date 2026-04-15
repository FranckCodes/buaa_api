<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClientStructureTypeSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('client_structure_types')->upsert([
            ['code' => 'individuel', 'label' => 'Individuel', 'description' => 'Personne physique agissant à titre individuel.', 'is_active' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'cooperative', 'label' => 'Coopérative', 'description' => 'Organisation coopérative reconnue.', 'is_active' => true, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'association', 'label' => 'Association', 'description' => 'Structure associative.', 'is_active' => true, 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'entreprise', 'label' => 'Entreprise', 'description' => 'Structure entrepreneuriale ou société.', 'is_active' => true, 'sort_order' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'autre', 'label' => 'Autre', 'description' => 'Autre type de structure.', 'is_active' => true, 'sort_order' => 99, 'created_at' => $now, 'updated_at' => $now],
        ], ['code'], ['label', 'description', 'is_active', 'sort_order', 'updated_at']);
    }
}
