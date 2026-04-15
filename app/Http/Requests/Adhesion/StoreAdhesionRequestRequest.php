<?php

namespace App\Http\Requests\Adhesion;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAdhesionRequestRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nom'                      => ['required', 'string', 'max:255'],
            'demandeur_type'           => ['required', Rule::in(['personne', 'organisation'])],
            'client_activity_type_id'  => ['nullable', 'integer', 'exists:client_activity_types,id'],
            'client_structure_type_id' => ['nullable', 'integer', 'exists:client_structure_types,id'],
            'representant'             => ['nullable', 'string', 'max:100'],
            'telephone'                => ['required', 'string', 'max:30'],
            'email'                    => ['nullable', 'email', 'max:255'],
            'adresse'                  => ['nullable', 'string'],
            'province'                 => ['nullable', 'string', 'max:100'],
            'date_demande'             => ['nullable', 'date'],
            'cotisation'               => ['nullable', 'numeric', 'min:0'],
            'membres_nombre'           => ['nullable', 'integer', 'min:0'],
            'superficie_totale'        => ['nullable', 'numeric', 'min:0'],
            'type_culture'             => ['nullable', 'string', 'max:255'],
            'experience_annees'        => ['nullable', 'integer', 'min:0'],
            'nombre_animaux'           => ['nullable', 'integer', 'min:0'],
            'type_elevage'             => ['nullable', 'string', 'max:100'],
        ];
    }
}
