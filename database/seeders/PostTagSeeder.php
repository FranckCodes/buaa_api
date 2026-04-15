<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PostTagSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('post_tags')->upsert([
            ['code' => 'recolte', 'label' => 'Récolte', 'description' => 'Publication liée aux récoltes.', 'is_active' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'meteo', 'label' => 'Météo', 'description' => 'Publication liée à la météo.', 'is_active' => true, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'marche', 'label' => 'Marché', 'description' => 'Publication liée au marché.', 'is_active' => true, 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'conseil', 'label' => 'Conseil', 'description' => 'Publication de conseil ou recommandation.', 'is_active' => true, 'sort_order' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'general', 'label' => 'Général', 'description' => 'Publication générale.', 'is_active' => true, 'sort_order' => 5, 'created_at' => $now, 'updated_at' => $now],
        ], ['code'], ['label', 'description', 'is_active', 'sort_order', 'updated_at']);
    }
}
