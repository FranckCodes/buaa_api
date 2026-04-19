<?php

namespace App\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ModerateReportRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'action'       => ['required', Rule::in(['validate', 'revision', 'reject'])],
            'validator_id' => ['required', 'string', 'exists:users,id'],
            'reason'       => ['nullable', 'string'],
        ];
    }
}
