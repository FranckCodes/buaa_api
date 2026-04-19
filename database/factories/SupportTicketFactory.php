<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Reference\SupportCategory;
use App\Models\SupportTicket;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupportTicketFactory extends Factory
{
    protected $model = SupportTicket::class;

    public function definition(): array
    {
        return [
            'id'                  => '#' . str_pad((string) fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'client_id'           => Client::factory(),
            'support_category_id' => SupportCategory::query()->first()?->id,
            'sujet'               => fake()->sentence(4),
            'description'         => fake()->paragraph(),
            'statut'              => 'ouvert',
            'traite_par'          => null,
            'resolved_at'         => null,
        ];
    }
}
