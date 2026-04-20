<?php

namespace App\Http\Resources;

use App\Http\Resources\Reference\ReferenceValueResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdhesionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'numero_membre'       => $this->numero_membre,
            'client'              => new ClientResource($this->whenLoaded('client')),
            'union'               => new UnionResource($this->whenLoaded('union')),
            'type'                => new ReferenceValueResource($this->whenLoaded('type')),
            'status'              => new ReferenceValueResource($this->whenLoaded('status')),
            'payment_mode'        => new ReferenceValueResource($this->whenLoaded('paymentMode')),
            'date_adhesion'       => $this->date_adhesion,
            'prochaine_echeance'  => $this->prochaine_echeance,
            'cotisation_initiale' => $this->cotisation_initiale,
            'cotisation_annuelle' => $this->cotisation_annuelle,
            'avantages'           => $this->avantages,
            'cotisations'         => CotisationResource::collection($this->whenLoaded('cotisations')),
            'documents'           => DocumentResource::collection($this->whenLoaded('documents')),
            'created_at'          => $this->created_at,
        ];
    }
}
