<?php

namespace App\Http\Resources;

use App\Http\Resources\Reference\ReferenceValueResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'user'                  => new UserResource($this->whenLoaded('user')),
            'date_naissance'        => $this->date_naissance,
            'lieu_naissance'        => $this->lieu_naissance,
            'sexe'                  => $this->sexe,
            'etat_civil'            => $this->etat_civil,
            'adresse_complete'      => $this->adresse_complete,
            'ville'                 => $this->ville,
            'province'              => $this->province,
            'territoire'            => $this->territoire,
            'activity_type'         => new ReferenceValueResource($this->whenLoaded('activityType')),
            'structure_type'        => new ReferenceValueResource($this->whenLoaded('structureType')),
            'profession_detaillee'  => $this->profession_detaillee,
            'experience_annees'     => $this->experience_annees,
            'superficie_exploitation' => $this->superficie_exploitation,
            'type_culture'          => $this->type_culture,
            'nombre_animaux'        => $this->nombre_animaux,
            'revenus_mensuels'      => $this->revenus_mensuels,
            'autres_sources_revenus' => $this->autres_sources_revenus,
            'banque_principale'     => $this->banque_principale,
            'numero_compte'         => $this->numero_compte,
            'reference'             => [
                'nom'       => $this->ref_nom,
                'telephone' => $this->ref_telephone,
                'relation'  => $this->ref_relation,
            ],
            'superviseur'           => new UserResource($this->whenLoaded('superviseur')),
            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at,
        ];
    }
}
