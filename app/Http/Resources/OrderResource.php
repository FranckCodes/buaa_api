<?php

namespace App\Http\Resources;

use App\Http\Resources\Reference\ReferenceValueResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'client'          => new ClientResource($this->whenLoaded('client')),
            'type'            => new ReferenceValueResource($this->whenLoaded('type')),
            'status'          => new ReferenceValueResource($this->whenLoaded('status')),
            'montant'         => $this->montant,
            'description'     => $this->description,
            'justification'   => $this->justification,
            'quantite'        => $this->quantite,
            'unite'           => $this->unite,
            'priorite'        => $this->priorite,
            'progression'     => $this->progression,
            'date_soumission' => $this->date_soumission,
            'treated_by'      => new UserResource($this->whenLoaded('treatedBy')),
            'tracking_steps'  => OrderTrackingResource::collection($this->whenLoaded('trackingSteps')),
            'documents'       => DocumentResource::collection($this->whenLoaded('documents')),
            'created_at'      => $this->created_at,
        ];
    }
}
