<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CreditStatusSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('credit_statuses')->upsert([
            ['code' => 'en_analyse', 'label' => 'En analyse', 'description' => "Dossier en cours d'étude.", 'is_active' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'actif', 'label' => 'Actif', 'description' => 'Crédit approuvé et actif.', 'is_active' => true, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'rembourse', 'label' => 'Remboursé', 'description' => 'Crédit totalement remboursé.', 'is_active' => true, 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'en_retard', 'label' => 'En retard', 'description' => 'Crédit avec retard de paiement.', 'is_active' => true, 'sort_order' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'rejete', 'label' => 'Rejeté', 'description' => 'Demande de crédit refusée.', 'is_active' => true, 'sort_order' => 5, 'created_at' => $now, 'updated_at' => $now],
        ], ['code'], ['label', 'description', 'is_active', 'sort_order', 'updated_at']);
    }
}
