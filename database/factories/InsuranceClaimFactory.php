<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Insurance;
use App\Models\InsuranceClaim;
use Illuminate\Database\Eloquent\Factories\Factory;

class InsuranceClaimFactory extends Factory
{
    protected $model = InsuranceClaim::class;

    public function definition(): array
    {
        return [
            'id'              => 'REC-' . now()->format('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 4)),
            'insurance_id'    => Insurance::factory(),
            'client_id'       => Client::factory(),
            'type_sinistre'   => fake()->randomElement(['accident', 'incendie', 'perte', 'dommage']),
            'montant_reclame' => fake()->randomFloat(2, 50, 5000),
            'montant_approuve' => null,
            'statut'          => 'en_analyse',
            'description'     => fake()->paragraph(),
            'date_sinistre'   => now()->subDays(3)->toDateString(),
            'date_soumission' => now()->toDateString(),
            'traite_par'      => null,
        ];
    }
}
