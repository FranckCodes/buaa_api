<?php

namespace Database\Factories;

use App\Models\Adhesion;
use App\Models\Client;
use App\Models\Reference\AdhesionStatus;
use App\Models\Reference\AdhesionType;
use App\Models\Reference\PaymentMode;
use App\Models\Union;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdhesionFactory extends Factory
{
    protected $model = Adhesion::class;

    public function definition(): array
    {
        return [
            'id'                  => 'ADH-' . now()->format('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 4)),
            'client_id'           => Client::factory(),
            'union_id'            => Union::factory(),
            'adhesion_type_id'    => AdhesionType::query()->first()?->id,
            'adhesion_status_id'  => AdhesionStatus::where('code', 'actif')->first()?->id,
            'numero_membre'       => 'MBR-' . now()->format('Y') . '-' . str_pad((string) fake()->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
            'date_adhesion'       => now()->toDateString(),
            'prochaine_echeance'  => now()->addYear()->toDateString(),
            'cotisation_initiale' => fake()->randomFloat(2, 10, 200),
            'cotisation_annuelle' => fake()->randomFloat(2, 10, 200),
            'payment_mode_id'     => PaymentMode::query()->first()?->id,
            'avantages'           => ['formation', 'réseau'],
        ];
    }
}
