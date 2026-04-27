<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Superviseur\StoreSuperviseurRequest;
use App\Http\Resources\SuperviseurResource;
use App\Models\Superviseur;
use App\Services\SupervisorZoneService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SuperviseurController extends Controller
{
    public function __construct(
        protected UserService $userService,
        protected SupervisorZoneService $zoneService
    ) {}

    public function store(StoreSuperviseurRequest $request): JsonResponse
    {
        $data = $request->validated();

        return DB::transaction(function () use ($data) {
            // 1. Créer le compte utilisateur avec rôle superviseur
            $user = $this->userService->createUser($data, ['superviseur']);

            // 2. Créer le profil superviseur
            $superviseur = Superviseur::create([
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

            // 3. Ajouter les zones de supervision
            if (!empty($data['zones'])) {
                foreach ($data['zones'] as $zone) {
                    $this->zoneService->addZone($superviseur, $zone);
                }
            }

            return $this->successResponse(
                new SuperviseurResource($superviseur->load(['user.roles', 'user.status', 'activeZones.province', 'activeZones.territoire', 'activeZones.secteur', 'activeZones.commune'])),
                'Superviseur créé avec succès.',
                201
            );
        });
    }

    public function index(): JsonResponse
    {
        $superviseurs = Superviseur::with(['user.roles', 'user.status', 'activeZones'])
            ->where('is_active', true)
            ->get();

        return $this->successResponse(
            SuperviseurResource::collection($superviseurs),
            'Liste des superviseurs récupérée avec succès.'
        );
    }

    public function show(Superviseur $superviseur): JsonResponse
    {
        return $this->successResponse(
            new SuperviseurResource($superviseur->load(['user.roles', 'user.status', 'activeZones.province', 'activeZones.territoire', 'activeZones.secteur', 'activeZones.commune', 'clients'])),
            'Superviseur récupéré avec succès.'
        );
    }
}
