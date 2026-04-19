<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Insurance;
use App\Models\Reference\InsuranceStatus;
use App\Models\Reference\InsuranceType;
use Illuminate\Database\Eloquent\Factories\Factory;

class InsuranceFactory extends Factory
{
    protected $model = Insurance::class;

    public function definition(): array
    {
        return [
            'id'                  => 'ASS-' . now()->format('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 4)),
            'client_id'           => Client::factory(),
            'insurance_type_id'   => InsuranceType::query()->first()?->id,
            'insurance_status_id' => InsuranceStatus::where('code', 'en_attente')->first()?->id,
            'montant_annuel'      => fake()->randomFloat(2, 50, 5000),
            'date_souscription'   => now()->toDateString(),
            'description'         => fake()->sentence(),
            'couvertures'         => ['base'],
            'traite_par'          => null,
        ];
    }
}
