<?php

namespace App\Http\Requests\Credit;

use Illuminate\Foundation\Http\FormRequest;

class StoreCreditRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'client_id'              => ['required', 'integer', 'exists:clients,id'],
            'credit_type_id'         => ['required', 'integer', 'exists:credit_types,id'],
            'montant_demande'        => ['required', 'numeric', 'min:0.01'],
            'date_demande'           => ['nullable', 'date'],
            'duree_mois'             => ['required', 'integer', 'min:1'],
            'taux_interet'           => ['nullable', 'numeric', 'min:0'],
            'objet_credit'           => ['nullable', 'string'],
            'description_projet'     => ['nullable', 'string'],
            'retour_investissement'  => ['nullable', 'string'],
            'revenus_mensuels'       => ['nullable', 'numeric', 'min:0'],
            'autres_credits'         => ['nullable', 'boolean'],
            'montant_autres_credits' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
