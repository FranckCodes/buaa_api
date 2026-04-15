<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InsuranceTypeSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('insurance_types')->upsert([
            ['code' => 'scolaire', 'label' => 'Scolaire', 'description' => 'Assurance scolaire.', 'is_active' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'exploitation', 'label' => 'Exploitation', 'description' => "Assurance liée à l'exploitation agricole.", 'is_active' => true, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'sante', 'label' => 'Santé', 'description' => 'Assurance santé.', 'is_active' => true, 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
        ], ['code'], ['label', 'description', 'is_active', 'sort_order', 'updated_at']);
    }
}
