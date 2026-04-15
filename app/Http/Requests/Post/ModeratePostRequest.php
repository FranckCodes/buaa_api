<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ModeratePostRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'action'       => ['required', Rule::in(['approve', 'reject'])],
            'validator_id' => ['required', 'integer', 'exists:users,id'],
            'reason'       => ['nullable', 'string'],
        ];
    }
}
