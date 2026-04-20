<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreditPaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'periode_annee' => $this->periode_annee,
            'periode_mois'  => $this->periode_mois,
            'montant'       => $this->montant,
            'statut'        => $this->statut,
            'date_paiement' => $this->date_paiement,
            'date_echeance' => $this->date_echeance,
        ];
    }
}
