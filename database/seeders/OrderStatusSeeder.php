<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderStatusSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('order_statuses')->upsert([
            ['code' => 'en_attente', 'label' => 'En attente', 'description' => 'Commande en attente de traitement.', 'is_active' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'approuve', 'label' => 'Approuvé', 'description' => 'Commande approuvée.', 'is_active' => true, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'en_cours', 'label' => 'En cours', 'description' => 'Commande en cours de traitement.', 'is_active' => true, 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'rejete', 'label' => 'Rejeté', 'description' => 'Commande rejetée.', 'is_active' => true, 'sort_order' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'livre', 'label' => 'Livré', 'description' => 'Commande livrée.', 'is_active' => true, 'sort_order' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'annule', 'label' => 'Annulé', 'description' => 'Commande annulée.', 'is_active' => true, 'sort_order' => 6, 'created_at' => $now, 'updated_at' => $now],
        ], ['code'], ['label', 'description', 'is_active', 'sort_order', 'updated_at']);
    }
}
