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
            'id'              => 'CMD-' . now()->format('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 4)),
            'client_id'       => Client::factory(),
            'order_type_id'   => OrderType::query()->first()?->id,
            'order_status_id' => OrderStatus::where('code', 'en_attente')->first()?->id,
            'montant'         => fake()->randomFloat(2, 0, 5000),
            'description'     => fake()->sentence(),
            'justification'   => fake()->paragraph(),
            'quantite'        => fake()->randomFloat(2, 1, 100),
            'unite'           => fake()->randomElement(['kg', 'pcs', 'litres', 'sacs']),
            'priorite'        => fake()->randomElement(['haute', 'moyenne', 'basse']),
            'progression'     => 0,
            'date_soumission' => now()->toDateString(),
            'traite_par'      => null,
        ];
    }
}
