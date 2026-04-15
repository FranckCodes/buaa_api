<?php

namespace App\Http\Requests\Insurance;

use Illuminate\Foundation\Http\FormRequest;

class ApproveInsuranceClaimRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'amount'       => ['required', 'numeric', 'min:0.01'],
            'processed_by' => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
