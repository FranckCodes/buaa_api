<?php

namespace App\Http\Requests\Messaging;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendMessageRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'sender_id'           => ['required', 'integer', 'exists:users,id'],
            'text'                => ['nullable', 'string'],
            'type'                => ['nullable', Rule::in(['text', 'image', 'file'])],
            'image_url'           => ['nullable', 'string', 'max:500'],
            'file_url'            => ['nullable', 'string', 'max:500'],
            'reply_to_message_id' => ['nullable', 'integer', 'exists:messages,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $type = $this->input('type', 'text');

            if ($type === 'text' && blank($this->input('text'))) {
                $validator->errors()->add('text', 'Le texte est requis pour un message de type text.');
            }
            if ($type === 'image' && blank($this->input('image_url'))) {
                $validator->errors()->add('image_url', 'image_url est requis pour un message de type image.');
            }
            if ($type === 'file' && blank($this->input('file_url'))) {
                $validator->errors()->add('file_url', 'file_url est requis pour un message de type file.');
            }
        });
    }
}
