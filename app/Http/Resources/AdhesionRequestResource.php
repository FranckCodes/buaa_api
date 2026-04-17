<?php

namespace App\Http\Resources;

use App\Http\Resources\Reference\ReferenceValueResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdhesionRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'nom'              => $this->nom,
            'demandeur_type'   => $this->demandeur_type,
            'activity_type'    => new ReferenceValueResource($this->whenLoaded('activityType')),
            'structure_type'   => new ReferenceValueResource($this->whenLoaded('structureType')),
            'representant'     => $this->representant,
            'telephone'        => $this->telephone,
            'email'            => $this->email,
            'adresse'          => $this->adresse,
            'province'         => $this->province,
            'date_demande'     => $this->date_demande,
            'cotisation'       => $this->cotisation,
            'statut'           => $this->statut,
            'membres_nombre'   => $this->membres_nombre,
            'superficie_totale' => $this->superficie_totale,
            'type_culture'     => $this->type_culture,
            'experience_annees' => $this->experience_annees,
            'nombre_animaux'   => $this->nombre_animaux,
            'type_elevage'     => $this->type_elevage,
            'treated_by'       => new UserResource($this->whenLoaded('treatedBy')),
            'created_at'       => $this->created_at,
        ];
    }
}
