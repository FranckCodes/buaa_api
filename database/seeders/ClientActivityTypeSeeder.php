<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClientActivityTypeSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('client_activity_types')->upsert([
            ['code' => 'agriculteur', 'label' => 'Agriculteur', 'description' => 'Personne exerçant principalement dans la culture agricole.', 'is_active' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'eleveur', 'label' => 'Éleveur', 'description' => "Personne exerçant principalement dans l'élevage.", 'is_active' => true, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'pisciculteur', 'label' => 'Pisciculteur', 'description' => "Personne exerçant dans l'élevage de poissons.", 'is_active' => true, 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'commercant', 'label' => 'Commerçant', 'description' => 'Acteur du commerce lié ou non à la chaîne agricole.', 'is_active' => true, 'sort_order' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'transformateur', 'label' => 'Transformateur', 'description' => 'Acteur spécialisé dans la transformation des produits.', 'is_active' => true, 'sort_order' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'autre', 'label' => 'Autre', 'description' => 'Autre activité principale non listée.', 'is_active' => true, 'sort_order' => 99, 'created_at' => $now, 'updated_at' => $now],
        ], ['code'], ['label', 'description', 'is_active', 'sort_order', 'updated_at']);
    }
}
