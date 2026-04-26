<?php

namespace App\Http\Requests\Adhesion;

use App\Models\AdhesionRequestValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidateAdhesionRequestRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'level'    => ['required', Rule::in([
                AdhesionRequestValidation::LEVEL_PRESIDENT,
                AdhesionRequestValidation::LEVEL_SUPERVISEUR,
                AdhesionRequestValidation::LEVEL_ADMIN,
            ])],
            'decision' => ['required', Rule::in([
                AdhesionRequestValidation::DECISION_APPROVED,
                AdhesionRequestValidation::DECISION_REJECTED,
            ])],
            'motif'    => ['nullable', 'string', 'required_if:decision,' . AdhesionRequestValidation::DECISION_REJECTED],
        ];
    }

    public function messages(): array
    {
        return [
            'motif.required_if' => "Un motif est obligatoire en cas de rejet.",
        ];
    }
}
