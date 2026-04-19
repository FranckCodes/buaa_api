<?php

namespace Database\Factories;

use App\Models\Reference\UserStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'id'                  => 'USR-' . str_pad((string) fake()->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
            'nom_complet'         => fake()->name(),
            'email'               => fake()->unique()->safeEmail(),
            'telephone'           => fake()->phoneNumber(),
            'password'            => Hash::make('password'),
            'user_status_id'      => UserStatus::where('code', 'actif')->first()?->id ?? 1,
            'photo_profil'        => null,
            'derniere_connexion'  => null,
        ];
    }
}
