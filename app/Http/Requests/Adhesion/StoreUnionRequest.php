<?php

namespace App\Http\Requests\Adhesion;

use Illuminate\Foundation\Http\FormRequest;

class StoreUnionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nom'                  => ['required', 'string', 'max:255'],
            'type'                 => ['required', 'string', 'max:100'],
            'province'             => ['nullable', 'string', 'max:100'],
            'ville'                => ['nullable', 'string', 'max:100'],
            'adresse'              => ['nullable', 'string'],
            'telephone'            => ['nullable', 'string', 'max:50'],
            'email'                => ['nullable', 'email', 'max:255'],
            'date_creation'        => ['nullable', 'date'],
            'president'            => ['nullable', 'string', 'max:255'],
            'secretaire'           => ['nullable', 'string', 'max:255'],
            'tresorier'            => ['nullable', 'string', 'max:255'],
            'commissaire'          => ['nullable', 'string', 'max:255'],
            'membres_total'        => ['nullable', 'integer', 'min:0'],
            'superficie_totale'    => ['nullable', 'numeric', 'min:0'],
            'cultures_principales' => ['nullable', 'array'],
            'services'             => ['nullable', 'array'],
        ];
    }
}
