<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderTypeSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('order_types')->upsert([
            ['code' => 'financier', 'label' => 'Financier', 'description' => 'Commande ou demande à caractère financier.', 'is_active' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'materiel', 'label' => 'Matériel', 'description' => 'Commande de matériel.', 'is_active' => true, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'intrants', 'label' => 'Intrants', 'description' => "Commande d'intrants agricoles.", 'is_active' => true, 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'assurance', 'label' => 'Assurance', 'description' => 'Commande ou demande liée à une assurance.', 'is_active' => true, 'sort_order' => 4, 'created_at' => $now, 'updated_at' => $now],
        ], ['code'], ['label', 'description', 'is_active', 'sort_order', 'updated_at']);
    }
}
