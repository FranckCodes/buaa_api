<?php

namespace App\Http\Resources;

use App\Http\Resources\Reference\ReferenceValueResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CotisationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'annee'           => $this->annee,
            'montant'         => $this->montant,
            'statut'          => $this->statut,
            'date_paiement'   => $this->date_paiement,
            'payment_mode'    => new ReferenceValueResource($this->whenLoaded('paymentMode')),
            'reference_recu'  => $this->reference_recu,
        ];
    }
}
