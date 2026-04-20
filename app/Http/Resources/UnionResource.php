<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'nom'                 => $this->nom,
            'type'                => $this->type,
            'province'            => $this->province,
            'ville'               => $this->ville,
            'adresse'             => $this->adresse,
            'telephone'           => $this->telephone,
            'email'               => $this->email,
            'date_creation'       => $this->date_creation,
            'membres_total'       => $this->membres_total,
            'superficie_totale'   => $this->superficie_totale,
            'cultures_principales' => $this->cultures_principales,
            'services'            => $this->services,
            'members'             => UnionMemberResource::collection($this->whenLoaded('members')),
            'created_at'          => $this->created_at,
        ];
    }
}
