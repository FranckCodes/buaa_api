<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(protected UserService $userService) {}

    /**
     * Liste tous les utilisateurs (admin seulement).
     */
    public function index(Request $request): JsonResponse
    {
        $users = User::with(['roles', 'status'])
            ->when($request->filled('role'), fn ($q) =>
                $q->whereHas('roles', fn ($r) => $r->where('code', $request->role))
            )
            ->when($request->filled('status'), fn ($q) =>
                $q->whereHas('status', fn ($s) => $s->where('code', $request->status))
            )
            ->when($request->filled('search'), fn ($q) =>
                $q->where(fn ($s) =>
                    $s->where('nom', 'like', '%' . $request->search . '%')
                      ->orWhere('postnom', 'like', '%' . $request->search . '%')
                      ->orWhere('prenom', 'like', '%' . $request->search . '%')
                      ->orWhere('email', 'like', '%' . $request->search . '%')
                      ->orWhere('login_code', 'like', '%' . $request->search . '%')
                )
            )
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return $this->successResponse(
            UserResource::collection($users)->response()->getData(true),
            'Liste des utilisateurs récupérée avec succès.'
        );
    }

    /**
     * Détail d'un utilisateur.
     */
    public function show(User $user): JsonResponse
    {
        return $this->successResponse(
            new UserResource($user->load(['roles', 'status', 'clientProfile'])),
            'Utilisateur récupéré avec succès.'
        );
    }

    /**
     * Mise à jour d'un utilisateur (rôles, statut).
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'nom'          => ['sometimes', 'string', 'max:100'],
            'postnom'      => ['sometimes', 'nullable', 'string', 'max:100'],
            'prenom'       => ['sometimes', 'string', 'max:100'],
            'telephone'    => ['sometimes', 'nullable', 'string', 'max:20'],
            'photo_profil' => ['sometimes', 'nullable', 'string'],
            'role_codes'   => ['sometimes', 'array'],
            'role_codes.*' => ['string'],
            'status_code'  => ['sometimes', 'string'],
        ]);

        if (!empty($data['role_codes'])) {
            $this->userService->syncRoles($user, $data['role_codes']);
        }

        if (!empty($data['status_code'])) {
            $this->userService->changeStatus($user, $data['status_code']);
        }

        $user->update(array_filter([
            'nom'          => $data['nom'] ?? null,
            'postnom'      => $data['postnom'] ?? null,
            'prenom'       => $data['prenom'] ?? null,
            'telephone'    => $data['telephone'] ?? null,
            'photo_profil' => $data['photo_profil'] ?? null,
        ], fn ($v) => !is_null($v)));

        return $this->successResponse(
            new UserResource($user->fresh(['roles', 'status'])),
            'Utilisateur mis à jour avec succès.'
        );
    }

    /**
     * Suppression d'un utilisateur.
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return $this->successResponse(null, 'Utilisateur supprimé avec succès.');
    }
}
