<?php

namespace Database\Factories;

use App\Models\Union;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnionFactory extends Factory
{
    protected $model = Union::class;

    public function definition(): array
    {
        return [
            'id'                   => 'UA-' . now()->format('Y') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 4)),
            'nom'                  => fake()->company(),
            'type'                 => fake()->randomElement(['union_agricole', 'union_elevage', 'union_cooperative']),
            'province'             => fake()->state(),
            'ville'                => fake()->city(),
            'adresse'              => fake()->address(),
            'telephone'            => fake()->phoneNumber(),
            'email'                => fake()->safeEmail(),
            'date_creation'        => now()->subYears(2)->toDateString(),
            'president'            => fake()->name(),
            'secretaire'           => fake()->name(),
            'tresorier'            => fake()->name(),
            'commissaire'          => fake()->name(),
            'membres_total'        => fake()->numberBetween(0, 300),
            'superficie_totale'    => fake()->randomFloat(2, 1, 1000),
            'cultures_principales' => ['maïs', 'manioc'],
            'services'             => ['formation', 'accompagnement'],
        ];
    }
}
