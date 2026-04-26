<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UnionStatusSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('union_statuses')->upsert([
            ['code' => 'suspendu',  'label' => 'Suspendu',  'description' => "Union créée, en attente de soumission/validation des documents officiels par le président.", 'is_active' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'en_revue',  'label' => 'En revue',  'description' => "Documents soumis par le président, en attente de validation par l'Admin.",                  'is_active' => true, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'active',    'label' => 'Active',    'description' => "Union opérationnelle, peut recevoir des adhésions.",                                       'is_active' => true, 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'desactivee','label' => 'Désactivée','description' => "Désactivation définitive par le Super Admin (irréversible).",                              'is_active' => true, 'sort_order' => 4, 'created_at' => $now, 'updated_at' => $now],
        ], ['code'], ['label', 'description', 'is_active', 'sort_order', 'updated_at']);
    }
}
