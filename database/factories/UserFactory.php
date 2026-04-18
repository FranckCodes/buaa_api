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
