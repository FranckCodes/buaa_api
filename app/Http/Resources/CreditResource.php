<?php

namespace App\Http\Resources;

use App\Http\Resources\Reference\ReferenceValueResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreditResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                     => $this->id,
            'client'                 => new ClientResource($this->whenLoaded('client')),
            'type'                   => new ReferenceValueResource($this->whenLoaded('type')),
            'status'                 => new ReferenceValueResource($this->whenLoaded('status')),
            'montant_demande'        => $this->montant_demande,
            'montant_approuve'       => $this->montant_approuve,
            'montant_rembourse'      => $this->montant_rembourse,
            'date_demande'           => $this->date_demande,
            'date_approbation'       => $this->date_approbation,
            'duree_mois'             => $this->duree_mois,
            'taux_interet'           => $this->taux_interet,
            'prochaine_echeance'     => $this->prochaine_echeance,
            'montant_echeance'       => $this->montant_echeance,
            'objet_credit'           => $this->objet_credit,
            'description_projet'     => $this->description_projet,
            'retour_investissement'  => $this->retour_investissement,
            'revenus_mensuels'       => $this->revenus_mensuels,
            'autres_credits'         => $this->autres_credits,
            'montant_autres_credits' => $this->montant_autres_credits,
            'treated_by'             => new UserResource($this->whenLoaded('treatedBy')),
            'payments'               => CreditPaymentResource::collection($this->whenLoaded('payments')),
            'business_plan'          => new BusinessPlanResource($this->whenLoaded('businessPlan')),
            'documents'              => DocumentResource::collection($this->whenLoaded('documents')),
            'created_at'             => $this->created_at,
        ];
    }
}
