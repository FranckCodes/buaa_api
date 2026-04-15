<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'client_id'       => ['required', 'integer', 'exists:clients,id'],
            'order_type_id'   => ['required', 'integer', 'exists:order_types,id'],
            'montant'         => ['nullable', 'numeric', 'min:0'],
            'description'     => ['nullable', 'string'],
            'justification'   => ['nullable', 'string'],
            'quantite'        => ['nullable', 'numeric', 'min:0'],
            'unite'           => ['nullable', 'string', 'max:50'],
            'priorite'        => ['nullable', Rule::in(['haute', 'moyenne', 'basse'])],
            'date_soumission' => ['nullable', 'date'],
        ];
    }
}
