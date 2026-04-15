<?php

namespace App\Http\Requests\Insurance;

use Illuminate\Foundation\Http\FormRequest;

class ActivateInsuranceRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'processed_by' => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
