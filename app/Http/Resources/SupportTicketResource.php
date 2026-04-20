<?php

namespace App\Http\Resources;

use App\Http\Resources\Reference\ReferenceValueResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupportTicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'client'      => new ClientResource($this->whenLoaded('client')),
            'category'    => new ReferenceValueResource($this->whenLoaded('category')),
            'sujet'       => $this->sujet,
            'description' => $this->description,
            'statut'      => $this->statut,
            'treated_by'  => new UserResource($this->whenLoaded('treatedBy')),
            'resolved_at' => $this->resolved_at,
            'created_at'  => $this->created_at,
        ];
    }
}
