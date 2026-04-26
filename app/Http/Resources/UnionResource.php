<?php

namespace App\Http\Resources;

use App\Http\Resources\Reference\ReferenceValueResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'nom'                  => $this->nom,
            'type'                 => $this->type,

            'status'               => new ReferenceValueResource($this->whenLoaded('status')),
            'president'            => new UserResource($this->whenLoaded('president')),

            'province_id'          => $this->province_id,
            'territoire_id'        => $this->territoire_id,
            'secteur_id'           => $this->secteur_id,
            'ville_id'             => $this->ville_id,
            'commune_id'           => $this->commune_id,

            'adresse'              => $this->adresse,
            'telephone'            => $this->telephone,
            'email'                => $this->email,
            'date_creation'        => $this->date_creation,

            'secretaire'           => $this->secretaire,
            'tresorier'            => $this->tresorier,
            'commissaire'          => $this->commissaire,

            'membres_total'        => $this->membres_total,
            'superficie_totale'    => $this->superficie_totale,
            'cultures_principales' => $this->cultures_principales,
            'services'             => $this->services,

            'validated_by'         => new UserResource($this->whenLoaded('validator')),
            'validated_at'         => $this->validated_at,
            'deactivated_by'       => new UserResource($this->whenLoaded('deactivator')),
            'deactivated_at'       => $this->deactivated_at,
            'deactivation_reason'  => $this->deactivation_reason,

            'members'              => UnionMemberResource::collection($this->whenLoaded('members')),
            'documents'            => DocumentResource::collection($this->whenLoaded('documents')),
            'created_at'           => $this->created_at,
        ];
    }
}
