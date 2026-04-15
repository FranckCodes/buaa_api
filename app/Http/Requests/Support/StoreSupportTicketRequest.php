<?php

namespace App\Http\Requests\Support;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupportTicketRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'client_id'           => ['required', 'integer', 'exists:clients,id'],
            'support_category_id' => ['required', 'integer', 'exists:support_categories,id'],
            'sujet'               => ['required', 'string', 'max:255'],
            'description'         => ['nullable', 'string'],
        ];
    }
}
