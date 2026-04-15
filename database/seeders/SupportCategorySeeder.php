<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SupportCategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('support_categories')->upsert([
            ['code' => 'credit', 'label' => 'Crédit', 'description' => 'Ticket lié au module crédit.', 'is_active' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'assurance', 'label' => 'Assurance', 'description' => 'Ticket lié au module assurance.', 'is_active' => true, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'adhesion', 'label' => 'Adhésion', 'description' => 'Ticket lié au module adhésion.', 'is_active' => true, 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'commande', 'label' => 'Commande', 'description' => 'Ticket lié au module commande.', 'is_active' => true, 'sort_order' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'technique', 'label' => 'Technique', 'description' => 'Ticket lié à un problème technique.', 'is_active' => true, 'sort_order' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'autre', 'label' => 'Autre', 'description' => 'Autre catégorie de support.', 'is_active' => true, 'sort_order' => 99, 'created_at' => $now, 'updated_at' => $now],
        ], ['code'], ['label', 'description', 'is_active', 'sort_order', 'updated_at']);
    }
}
