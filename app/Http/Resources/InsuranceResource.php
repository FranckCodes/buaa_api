<?php

namespace App\Http\Resources;

use App\Http\Resources\Reference\ReferenceValueResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InsuranceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'client'               => new ClientResource($this->whenLoaded('client')),
            'type'                 => new ReferenceValueResource($this->whenLoaded('type')),
            'status'               => new ReferenceValueResource($this->whenLoaded('status')),
            'montant_annuel'       => $this->montant_annuel,
            'date_souscription'    => $this->date_souscription,
            'date_debut'           => $this->date_debut,
            'date_fin'             => $this->date_fin,
            'prochaine_echeance'   => $this->prochaine_echeance,
            'description'          => $this->description,
            'couvertures'          => $this->couvertures,
            'etablissement'        => $this->etablissement,
            'niveau_etude'         => $this->niveau_etude,
            'superficie_hectares'  => $this->superficie_hectares,
            'type_culture'         => $this->type_culture,
            'valeur_materiel'      => $this->valeur_materiel,
            'antecedents_medicaux' => $this->antecedents_medicaux,
            'medecin_traitant'     => $this->medecin_traitant,
            'treated_by'           => new UserResource($this->whenLoaded('treatedBy')),
            'beneficiaries'        => InsuranceBeneficiaryResource::collection($this->whenLoaded('beneficiaries')),
            'claims'               => InsuranceClaimResource::collection($this->whenLoaded('claims')),
            'documents'            => DocumentResource::collection($this->whenLoaded('documents')),
            'created_at'           => $this->created_at,
        ];
    }
}
