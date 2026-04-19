<?php

namespace Database\Factories;

use App\Models\AdhesionRequest;
use App\Models\Reference\ClientActivityType;
use App\Models\Reference\ClientStructureType;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdhesionRequestFactory extends Factory
{
    protected $model = AdhesionRequest::class;

    public function definition(): array
    {
        return [
            'id'                       => 'ADH-' . now()->format('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 4)),
            'nom'                      => fake()->company(),
            'demandeur_type'           => fake()->randomElement(['personne', 'organisation']),
            'client_activity_type_id'  => ClientActivityType::query()->first()?->id,
            'client_structure_type_id' => ClientStructureType::query()->first()?->id,
            'representant'             => fake()->name(),
            'telephone'                => fake()->phoneNumber(),
            'email'                    => fake()->safeEmail(),
            'adresse'                  => fake()->address(),
            'province'                 => fake()->state(),
            'date_demande'             => now()->toDateString(),
            'cotisation'               => fake()->randomFloat(2, 10, 500),
            'statut'                   => 'en_attente',
            'membres_nombre'           => fake()->numberBetween(1, 100),
            'superficie_totale'        => fake()->randomFloat(2, 0, 500),
            'type_culture'             => fake()->word(),
            'experience_annees'        => fake()->numberBetween(0, 20),
            'nombre_animaux'           => fake()->numberBetween(0, 300),
            'type_elevage'             => fake()->word(),
            'traite_par'               => null,
        ];
    }
}
