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
            'id'                     => $this->id,
            'nom'                    => $this->nom,
            'demandeur_type'         => $this->demandeur_type,

            'union_id'               => $this->union_id,
            'union'                  => new UnionResource($this->whenLoaded('union')),
            'client_id'              => $this->client_id,

            'activity_type'          => new ReferenceValueResource($this->whenLoaded('activityType')),
            'structure_type'         => new ReferenceValueResource($this->whenLoaded('structureType')),

            'representant'           => $this->representant,
            'telephone'              => $this->telephone,
            'email'                  => $this->email,
            'adresse'                => $this->adresse,

            'province_id'            => $this->province_id,
            'territoire_id'          => $this->territoire_id,
            'secteur_id'             => $this->secteur_id,
            'ville_id'               => $this->ville_id,
            'commune_id'             => $this->commune_id,

            'date_demande'           => $this->date_demande,
            'cotisation'             => $this->cotisation,

            'statut'                 => $this->statut,
            'etape_courante'         => $this->etape_courante,
            'motif_rejet'            => $this->motif_rejet,
            'numero_membre_attribue' => $this->numero_membre_attribue,

            'membres_nombre'         => $this->membres_nombre,
            'superficie_totale'      => $this->superficie_totale,
            'type_culture'           => $this->type_culture,
            'experience_annees'      => $this->experience_annees,
            'nombre_animaux'         => $this->nombre_animaux,
            'type_elevage'           => $this->type_elevage,

            'validations'            => AdhesionRequestValidationResource::collection($this->whenLoaded('validations')),
            'documents'              => DocumentResource::collection($this->whenLoaded('documents')),
            'treated_by'             => new UserResource($this->whenLoaded('treatedBy')),
            'created_at'             => $this->created_at,
        ];
    }
}
