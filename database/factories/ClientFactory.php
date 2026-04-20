<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Reference\ClientActivityType;
use App\Models\Reference\ClientStructureType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        return [
            'id'                       => User::factory(),
            'date_naissance'           => fake()->date(),
            'lieu_naissance'           => fake()->city(),
            'sexe'                     => fake()->randomElement(['M', 'F']),
            'etat_civil'               => fake()->randomElement(['celibataire', 'marie', 'divorce', 'veuf']),
            'adresse_complete'         => fake()->address(),
            'ville'                    => fake()->city(),
            'province'                 => fake()->state(),
            'territoire'               => fake()->city(),
            'client_activity_type_id'  => ClientActivityType::first()?->id,
            'client_structure_type_id' => ClientStructureType::first()?->id,
            'profession_detaillee'     => fake()->jobTitle(),
            'experience_annees'        => fake()->numberBetween(0, 20),
            'superficie_exploitation'  => fake()->randomFloat(2, 1, 100),
            'type_culture'             => fake()->word(),
            'nombre_animaux'           => fake()->numberBetween(0, 100),
            'revenus_mensuels'         => fake()->randomFloat(2, 100, 10000),
            'autres_sources_revenus'   => fake()->sentence(),
            'banque_principale'        => fake()->company(),
            'numero_compte'            => fake()->iban(),
            'ref_nom'                  => fake()->name(),
            'ref_telephone'            => fake()->phoneNumber(),
            'ref_relation'             => 'Frère',
            'superviseur_id'           => null,
        ];
    }
}
