<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function login(array $credentials): array
    {
        if (!Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants sont incorrects.'],
            ]);
        }

        /** @var User $user */
        $user = Auth::user();
        $user->update(['derniere_connexion' => now()]);

        $token = $user->createToken('api')->plainTextToken;

        return [
            'user'  => $user->load('roles', 'status'),
            'token' => $token,
        ];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    public function register(array $data, array $roleCodes = ['client']): array
    {
        $user = $this->userService->createUser($data, $roleCodes);
        $token = $user->createToken('api')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }
}
