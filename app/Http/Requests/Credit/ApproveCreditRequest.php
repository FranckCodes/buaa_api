<?php

namespace App\Http\Requests\Credit;

use Illuminate\Foundation\Http\FormRequest;

class ApproveCreditRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'montant_approuve'   => ['required', 'numeric', 'min:0.01'],
            'montant_echeance'   => ['nullable', 'numeric', 'min:0'],
            'prochaine_echeance' => ['nullable', 'date'],
            'traite_par'         => ['required', 'string', 'exists:users,id'],
        ];
    }
}
