<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SuperviseurResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                     => $this->id,
            'user'                   => new UserResource($this->whenLoaded('user')),
            'matricule'              => $this->matricule,
            'telephone_pro'          => $this->telephone_pro,
            'notes'                  => $this->notes,
            'is_active'              => $this->is_active,
            'date_naissance'         => $this->date_naissance,
            'lieu_naissance'         => $this->lieu_naissance,
            'sexe'                   => $this->sexe,
            'etat_civil'             => $this->etat_civil,
            'nationalite'            => $this->nationalite,
            'adresse_complete'       => $this->adresse_complete,
            'province'               => $this->whenLoaded('province', fn () => ['id' => $this->province->id, 'designation' => $this->province->designation]),
            'territoire'             => $this->whenLoaded('territoire', fn () => ['id' => $this->territoire->id, 'designation' => $this->territoire->designation]),
            'secteur'                => $this->whenLoaded('secteur', fn () => ['id' => $this->secteur->id, 'designation' => $this->secteur->designation]),
            'ville'                  => $this->whenLoaded('ville', fn () => ['id' => $this->ville->id, 'designation' => $this->ville->designation]),
            'commune'                => $this->whenLoaded('commune', fn () => ['id' => $this->commune->id, 'designation' => $this->commune->designation]),
            'niveau_etude'           => $this->niveau_etude,
            'specialite'             => $this->specialite,
            'experience_annees'      => $this->experience_annees,
            'type_piece_identite'    => $this->type_piece_identite,
            'numero_piece_identite'  => $this->numero_piece_identite,
            'zones'                  => $this->whenLoaded('activeZones', fn () =>
                $this->activeZones->map(fn ($z) => [
                    'id'             => $z->id,
                    'description'    => $z->description,
                    'province'       => $z->province?->designation,
                    'territoire'     => $z->territoire?->designation,
                    'secteur'        => $z->secteur?->designation,
                    'commune'        => $z->commune?->designation,
                ])
            ),
            'created_at'             => $this->created_at,
        ];
    }
}
