<?php

namespace App\Http\Requests\Insurance;

use Illuminate\Foundation\Http\FormRequest;

class StoreInsuranceRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'client_id'              => ['required', 'string', 'exists:clients,id'],
            'insurance_type_id'      => ['required', 'integer', 'exists:insurance_types,id'],
            'montant_annuel'         => ['required', 'numeric', 'min:0.01'],
            'date_souscription'      => ['nullable', 'date'],
            'description'            => ['nullable', 'string'],
            'couvertures'            => ['nullable', 'array'],
            'etablissement'          => ['nullable', 'string', 'max:255'],
            'niveau_etude'           => ['nullable', 'string', 'max:100'],
            'superficie_hectares'    => ['nullable', 'numeric', 'min:0'],
            'type_culture'           => ['nullable', 'string', 'max:255'],
            'valeur_materiel'        => ['nullable', 'numeric', 'min:0'],
            'antecedents_medicaux'   => ['nullable', 'string'],
            'medecin_traitant'       => ['nullable', 'string', 'max:255'],
            'beneficiaries'          => ['nullable', 'array'],
            'beneficiaries.*.nom'    => ['required_with:beneficiaries', 'string', 'max:255'],
            'beneficiaries.*.age'    => ['nullable', 'integer', 'min:0'],
            'beneficiaries.*.relation' => ['nullable', 'string', 'max:100'],
        ];
    }
}
