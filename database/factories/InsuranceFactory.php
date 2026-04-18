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
            'client_id'           => Client::factory(),
            'insurance_type_id'   => InsuranceType::first()?->id,
            'insurance_status_id' => InsuranceStatus::where('code', 'en_attente')->first()?->id,
            'montant_annuel'      => fake()->randomFloat(2, 50, 1000),
            'date_souscription'   => now()->toDateString(),
            'description'         => fake()->sentence(),
            'traite_par'          => null,
        ];
    }
}
