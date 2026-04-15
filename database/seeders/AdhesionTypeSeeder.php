<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdhesionTypeSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('adhesion_types')->upsert([
            ['code' => 'union_agricole', 'label' => 'Union agricole', 'description' => 'Adhésion à une union agricole.', 'is_active' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'union_elevage', 'label' => "Union d'élevage", 'description' => "Adhésion à une union d'élevage.", 'is_active' => true, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'union_cooperative', 'label' => 'Union coopérative', 'description' => 'Adhésion à une union coopérative.', 'is_active' => true, 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
        ], ['code'], ['label', 'description', 'is_active', 'sort_order', 'updated_at']);
    }
}
