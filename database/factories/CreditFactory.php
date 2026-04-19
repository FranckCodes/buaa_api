<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Credit;
use App\Models\Reference\CreditStatus;
use App\Models\Reference\CreditType;
use Illuminate\Database\Eloquent\Factories\Factory;

class CreditFactory extends Factory
{
    protected $model = Credit::class;

    public function definition(): array
    {
        return [
            'id'                     => 'CRD-' . now()->format('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 4)),
            'client_id'              => Client::factory(),
            'credit_type_id'         => CreditType::query()->first()?->id,
            'credit_status_id'       => CreditStatus::where('code', 'en_analyse')->first()?->id,
            'montant_demande'        => fake()->randomFloat(2, 100, 5000),
            'montant_approuve'       => null,
            'montant_rembourse'      => 0,
            'date_demande'           => now()->toDateString(),
            'date_approbation'       => null,
            'duree_mois'             => fake()->numberBetween(3, 24),
            'taux_interet'           => fake()->randomFloat(2, 0, 15),
            'prochaine_echeance'     => null,
            'montant_echeance'       => null,
            'objet_credit'           => fake()->sentence(),
            'description_projet'     => fake()->paragraph(),
            'retour_investissement'  => fake()->sentence(),
            'revenus_mensuels'       => fake()->randomFloat(2, 100, 2000),
            'autres_credits'         => false,
            'montant_autres_credits' => null,
            'traite_par'             => null,
        ];
    }
}
