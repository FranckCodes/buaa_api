<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportTypeSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('report_types')->upsert([
            ['code' => 'harvest', 'label' => 'Récolte', 'description' => 'Rapport lié à la récolte.', 'is_active' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'financial', 'label' => 'Financier', 'description' => 'Rapport financier.', 'is_active' => true, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'activity', 'label' => 'Activité', 'description' => "Rapport d'activité.", 'is_active' => true, 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
        ], ['code'], ['label', 'description', 'is_active', 'sort_order', 'updated_at']);
    }
}
