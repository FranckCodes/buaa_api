<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentModeSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('payment_modes')->upsert([
            ['code' => 'especes', 'label' => 'Espèces', 'description' => 'Paiement en espèces.', 'is_active' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'mobile_money', 'label' => 'Mobile Money', 'description' => 'Paiement via Mobile Money.', 'is_active' => true, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'virement', 'label' => 'Virement', 'description' => 'Paiement par virement bancaire.', 'is_active' => true, 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
        ], ['code'], ['label', 'description', 'is_active', 'sort_order', 'updated_at']);
    }
}
