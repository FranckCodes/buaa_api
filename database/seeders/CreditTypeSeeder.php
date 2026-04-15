<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CreditTypeSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('credit_types')->upsert([
            ['code' => 'materiel', 'label' => 'Matériel', 'description' => 'Crédit pour achat de matériel.', 'is_active' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'exploitation', 'label' => 'Exploitation', 'description' => 'Crédit pour exploitation agricole ou élevage.', 'is_active' => true, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'semences', 'label' => 'Semences', 'description' => 'Crédit pour semences et intrants liés.', 'is_active' => true, 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
        ], ['code'], ['label', 'description', 'is_active', 'sort_order', 'updated_at']);
    }
}
