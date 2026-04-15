<?php

namespace App\Http\Requests\Document;

use Illuminate\Foundation\Http\FormRequest;

class AttachDocumentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'type_document' => ['required', 'string', 'max:100'],
            'nom_fichier'   => ['required', 'string', 'max:255'],
            'url'           => ['required', 'string', 'max:500'],
            'taille_bytes'  => ['nullable', 'integer', 'min:0'],
            'mime_type'     => ['nullable', 'string', 'max:100'],
            'uploaded_by'   => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}
