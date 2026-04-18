<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Order;
use App\Models\Reference\OrderStatus;
use App\Models\Reference\OrderType;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'client_id'       => Client::factory(),
            'order_type_id'   => OrderType::first()?->id,
            'order_status_id' => OrderStatus::where('code', 'en_attente')->first()?->id,
            'montant'         => fake()->randomFloat(2, 50, 5000),
            'description'     => fake()->sentence(),
            'justification'   => fake()->sentence(),
            'quantite'        => fake()->randomFloat(2, 1, 100),
            'unite'           => 'kg',
            'priorite'        => 'moyenne',
            'progression'     => 0,
            'date_soumission' => now()->toDateString(),
            'traite_par'      => null,
        ];
    }
}
