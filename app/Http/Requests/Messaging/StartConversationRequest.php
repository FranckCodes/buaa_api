<?php

namespace App\Http\Requests\Messaging;

use Illuminate\Foundation\Http\FormRequest;

class StartConversationRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'participant_ids'   => ['required', 'array', 'min:2'],
            'participant_ids.*' => ['integer', 'exists:users,id'],
        ];
    }
}
