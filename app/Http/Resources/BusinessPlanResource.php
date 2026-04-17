<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessPlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'titre'                 => $this->titre,
            'resume'                => $this->resume,
            'description'           => $this->description,
            'retour_investissement' => $this->retour_investissement,
            'statut'                => $this->statut,
            'score'                 => $this->score,
            'date_soumission'       => $this->date_soumission,
            'evaluator'             => new UserResource($this->whenLoaded('evaluator')),
        ];
    }
}
