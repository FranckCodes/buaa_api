<?php

namespace App\Http\Requests\Adhesion;

use Illuminate\Foundation\Http\FormRequest;

class DeactivateUnionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'min:10'],
        ];
    }

    public function messages(): array
    {
        return [
            'reason.required' => "Une justification écrite est obligatoire pour la désactivation définitive d'une union.",
            'reason.min'      => "La justification doit contenir au moins 10 caractères.",
        ];
    }
}
