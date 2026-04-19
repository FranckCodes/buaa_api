<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition(): array
    {
        return [
            'documentable_id'   => 1,
            'documentable_type' => 'App\\Models\\Client',
            'type_document'     => fake()->randomElement(['piece_identite', 'business_plan', 'recu', 'contrat', 'photo', 'rapport']),
            'nom_fichier'       => fake()->word() . '.pdf',
            'url'               => fake()->url(),
            'taille_bytes'      => fake()->numberBetween(1000, 5000000),
            'mime_type'         => 'application/pdf',
            'uploaded_by'       => User::factory(),
        ];
    }
}
