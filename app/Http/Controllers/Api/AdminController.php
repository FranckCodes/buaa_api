<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdminRequest;
use App\Http\Resources\AdminResource;
use App\Models\Admin;
use App\Models\Province;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function __construct(protected UserService $userService) {}

    public function store(StoreAdminRequest $request): JsonResponse
    {
        $data = $request->validated();

        return DB::transaction(function () use ($data) {
            // 1. Créer le compte utilisateur avec rôle admin
            $user = $this->userService->createUser($data, ['admin']);

            // 2. Créer le profil admin
            $admin = Admin::create([
                'id'                     => $user->id,
                'matricule'              => $data['matricule'] ?? null,
                'telephone_pro'          => $data['telephone_pro'] ?? null,
                'notes'                  => $data['notes'] ?? null,
                'is_active'              => $data['is_active'] ?? true,
                'date_naissance'         => $data['date_naissance'] ?? null,
                'lieu_naissance'         => $data['lieu_naissance'] ?? null,
                'sexe'                   => $data['sexe'] ?? null,
                'etat_civil'             => $data['etat_civil'] ?? null,
                'nationalite'            => $data['nationalite'] ?? null,
                'adresse_complete'       => $data['adresse_complete'] ?? null,
                'province_id'            => $data['province_id'] ?? null,
                'territoire_id'          => $data['territoire_id'] ?? null,
                'secteur_id'             => $data['secteur_id'] ?? null,
                'ville_id'               => $data['ville_id'] ?? null,
                'commune_id'             => $data['commune_id'] ?? null,
                'niveau_etude'           => $data['niveau_etude'] ?? null,
                'specialite'             => $data['specialite'] ?? null,
                'experience_annees'      => $data['experience_annees'] ?? 0,
                'type_piece_identite'    => $data['type_piece_identite'] ?? null,
                'numero_piece_identite'  => $data['numero_piece_identite'] ?? null,
            ]);

            // 3. Assigner les provinces à charge
            if (!empty($data['provinces'])) {
                $admin->provinces()->sync(
                    collect($data['provinces'])->mapWithKeys(fn ($id) => [$id => ['is_active' => true]])->all()
                );
            }

            return $this->successResponse(
                new AdminResource($admin->load(['user.roles', 'user.status', 'activeProvinces'])),
                'Admin créé avec succès.',
                201
            );
        });
    }

    public function index(): JsonResponse
    {
        $admins = Admin::with(['user.roles', 'user.status', 'activeProvinces'])
            ->where('is_active', true)
            ->get();

        return $this->successResponse(
            AdminResource::collection($admins),
            'Liste des admins récupérée avec succès.'
        );
    }

    public function show(Admin $admin): JsonResponse
    {
        return $this->successResponse(
            new AdminResource($admin->load(['user.roles', 'user.status', 'activeProvinces'])),
            'Admin récupéré avec succès.'
        );
    }
}
