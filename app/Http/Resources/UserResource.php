<?php

namespace App\Http\Resources;

use App\Http\Resources\Reference\ReferenceValueResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'nom'                 => $this->nom,
            'postnom'             => $this->postnom,
            'prenom'              => $this->prenom,
            'nom_complet'         => $this->nom_complet,
            'email'               => $this->email,
            'login_code'          => $this->login_code,
            'telephone'           => $this->telephone,
            'photo_profil'        => $this->photo_profil,
            'derniere_connexion'  => $this->derniere_connexion,
            'status'              => new ReferenceValueResource($this->whenLoaded('status')),
            'roles'               => ReferenceValueResource::collection($this->whenLoaded('roles')),
            'client_profile'      => new ClientResource($this->whenLoaded('clientProfile')),
            'created_at'          => $this->created_at,
        ];
    }
}
