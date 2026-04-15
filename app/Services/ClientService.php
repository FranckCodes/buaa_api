<?php

namespace App\Services;

use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ClientService
{
    public function createClientProfile(User $user, array $data): Client
    {
        return DB::transaction(function () use ($user, $data) {
            return Client::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'date_naissance'           => $data['date_naissance'] ?? null,
                    'lieu_naissance'           => $data['lieu_naissance'] ?? null,
                    'sexe'                     => $data['sexe'] ?? null,
                    'etat_civil'               => $data['etat_civil'] ?? null,
                    'adresse_complete'         => $data['adresse_complete'] ?? null,
                    'ville'                    => $data['ville'] ?? null,
                    'province'                 => $data['province'] ?? null,
                    'territoire'               => $data['territoire'] ?? null,
                    'client_activity_type_id'  => $data['client_activity_type_id'] ?? null,
                    'client_structure_type_id' => $data['client_structure_type_id'] ?? null,
                    'profession_detaillee'     => $data['profession_detaillee'] ?? null,
                    'experience_annees'        => $data['experience_annees'] ?? 0,
                    'superficie_exploitation'  => $data['superficie_exploitation'] ?? null,
                    'type_culture'             => $data['type_culture'] ?? null,
                    'nombre_animaux'           => $data['nombre_animaux'] ?? null,
                    'revenus_mensuels'         => $data['revenus_mensuels'] ?? null,
                    'autres_sources_revenus'   => $data['autres_sources_revenus'] ?? null,
                    'banque_principale'        => $data['banque_principale'] ?? null,
                    'numero_compte'            => $data['numero_compte'] ?? null,
                    'ref_nom'                  => $data['ref_nom'] ?? null,
                    'ref_telephone'            => $data['ref_telephone'] ?? null,
                    'ref_relation'             => $data['ref_relation'] ?? null,
                    'superviseur_id'           => $data['superviseur_id'] ?? null,
                ]
            );
        });
    }

    public function assignSupervisor(Client $client, int $superviseurId): Client
    {
        $client->update(['superviseur_id' => $superviseurId]);

        return $client->fresh('superviseur');
    }
}
