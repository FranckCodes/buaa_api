<?php

namespace App\Http\Requests\Insurance;

use Illuminate\Foundation\Http\FormRequest;

class StoreInsuranceClaimRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'insurance_id'    => ['required', 'integer', 'exists:insurances,id'],
            'client_id'       => ['required', 'integer', 'exists:clients,id'],
            'type_sinistre'   => ['required', 'string', 'max:255'],
            'montant_reclame' => ['required', 'numeric', 'min:0.01'],
            'description'     => ['nullable', 'string'],
            'date_sinistre'   => ['nullable', 'date'],
            'date_soumission' => ['nullable', 'date'],
        ];
    }
}
