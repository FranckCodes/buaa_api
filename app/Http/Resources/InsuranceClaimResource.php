<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InsuranceClaimResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'insurance'        => new InsuranceResource($this->whenLoaded('insurance')),
            'client'           => new ClientResource($this->whenLoaded('client')),
            'type_sinistre'    => $this->type_sinistre,
            'montant_reclame'  => $this->montant_reclame,
            'montant_approuve' => $this->montant_approuve,
            'statut'           => $this->statut,
            'description'      => $this->description,
            'date_sinistre'    => $this->date_sinistre,
            'date_soumission'  => $this->date_soumission,
            'treated_by'       => new UserResource($this->whenLoaded('treatedBy')),
            'documents'        => DocumentResource::collection($this->whenLoaded('documents')),
            'created_at'       => $this->created_at,
        ];
    }
}
