<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(RegisterUserRequest $request, UserService $userService): JsonResponse
    {
        $data = $request->validated();
        $user = $userService->createUser($data, $data['role_codes']);
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse(
            ['user' => new UserResource($user->load('roles', 'status')), 'token' => $token],
            'Utilisateur créé avec succès.',
            201
        );
    }

    public function login(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->validated())) {
            return $this->errorResponse('Identifiants invalides.', 422);
        }

        $user = $request->user()->load('roles', 'status', 'clientProfile');
        $user->update(['derniere_connexion' => now()]);
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse(
            ['user' => new UserResource($user), 'token' => $token],
            'Connexion réussie.'
        );
    }

    public function me(Request $request): JsonResponse
    {
        return $this->successResponse(
            new UserResource($request->user()->load('roles', 'status', 'clientProfile')),
            'Utilisateur authentifié.'
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return $this->successResponse(null, 'Déconnexion réussie.');
    }
}
