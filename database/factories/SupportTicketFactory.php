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
            'client_id'           => Client::factory(),
            'support_category_id' => SupportCategory::first()?->id,
            'sujet'               => fake()->sentence(),
            'description'         => fake()->paragraph(),
            'statut'              => 'ouvert',
            'traite_par'          => null,
        ];
    }
}
