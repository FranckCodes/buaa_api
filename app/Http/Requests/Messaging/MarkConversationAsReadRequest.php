<?php

namespace App\Http\Requests\Messaging;

use Illuminate\Foundation\Http\FormRequest;

class MarkConversationAsReadRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'string', 'exists:users,id'],
        ];
    }
}
