<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Requests\Auth\RequestOtpLoginRequest;
use App\Http\Requests\Auth\VerifyOtpLoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\OtpService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterUserRequest $request, UserService $userService): JsonResponse
    {
        $data  = $request->validated();
        $user  = $userService->createUser($data, $data['role_codes']);
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse(
            ['user' => new UserResource($user->load('roles', 'status')), 'token' => $token],
            'Utilisateur créé avec succès.',
            201
        );
    }

    /**
     * Connexion par email ou login_code + password.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $login = $request->string('login')->toString();

        $user = User::with(['roles', 'status', 'clientProfile'])
            ->where('email', $login)
            ->orWhere('login_code', $login)
            ->first();

        if (!$user || !Hash::check($request->string('password')->toString(), $user->password)) {
            return $this->errorResponse('Identifiants invalides.', 422);
        }

        if ($user->status?->code !== 'actif') {
            return $this->errorResponse('Ce compte n\'est pas actif.', 403);
        }

        $user->update(['derniere_connexion' => now()]);
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse(
            ['user' => new UserResource($user->fresh(['roles', 'status', 'clientProfile'])), 'token' => $token],
            'Connexion réussie.'
        );
    }

    /**
     * Étape 1 OTP : demander un code par SMS.
     */
    public function requestOtpLogin(
        RequestOtpLoginRequest $request,
        OtpService $otpService
    ): JsonResponse {
        try {
            $otpService->generateLoginOtp($request->string('telephone')->toString());

            return $this->successResponse(null, 'Code OTP envoyé avec succès.');
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    /**
     * Étape 2 OTP : vérifier le code et connecter.
     */
    public function verifyOtpLogin(
        VerifyOtpLoginRequest $request,
        OtpService $otpService
    ): JsonResponse {
        try {
            $user = $otpService->verifyLoginOtp(
                $request->string('telephone')->toString(),
                $request->string('code')->toString()
            );

            $user->update(['derniere_connexion' => now()]);
            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->successResponse(
                ['user' => new UserResource($user), 'token' => $token],
                'Connexion OTP réussie.'
            );
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
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
