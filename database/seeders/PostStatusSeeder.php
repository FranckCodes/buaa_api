<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PostStatusSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('post_statuses')->upsert([
            ['code' => 'pending', 'label' => 'En attente', 'description' => 'Publication en attente de validation.', 'is_active' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'approved', 'label' => 'Approuvé', 'description' => 'Publication approuvée.', 'is_active' => true, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'rejected', 'label' => 'Rejeté', 'description' => 'Publication rejetée.', 'is_active' => true, 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
        ], ['code'], ['label', 'description', 'is_active', 'sort_order', 'updated_at']);
    }
}
