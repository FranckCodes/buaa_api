<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnionMemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'user'            => new UserResource($this->whenLoaded('user')),
            'nom_complet'     => $this->nom_complet,
            'telephone'       => $this->telephone,
            'role_dans_union' => $this->role_dans_union,
            'date_debut'      => $this->date_debut,
            'date_fin'        => $this->date_fin,
        ];
    }
}
