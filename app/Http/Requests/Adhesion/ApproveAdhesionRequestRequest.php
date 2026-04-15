<?php

namespace App\Http\Requests\Adhesion;

use Illuminate\Foundation\Http\FormRequest;

class ApproveAdhesionRequestRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'client_id'       => ['required', 'integer', 'exists:clients,id'],
            'union_id'        => ['required', 'integer', 'exists:unions,id'],
            'adhesion_type_id' => ['required', 'integer', 'exists:adhesion_types,id'],
            'payment_mode_id' => ['nullable', 'integer', 'exists:payment_modes,id'],
            'processed_by'    => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
