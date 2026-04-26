<?php

namespace App\Http\Requests\Adhesion;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUnionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nom'                  => ['required', 'string', 'max:255'],
            'type'                 => ['required', 'string', 'max:100'],

            'president_id'         => ['required', 'string', 'max:50', 'exists:users,id'],

            // Géo : province obligatoire ; ensuite, soit territoire (rural), soit commune (urbain/Kinshasa)
            'province_id'          => ['required', 'integer', 'exists:provinces,id'],
            'territoire_id'        => ['nullable', 'required_without:commune_id', 'integer', 'exists:territoires,id'],
            'secteur_id'           => ['nullable', 'integer', 'exists:secteurs,id'],
            'ville_id'             => ['nullable', 'integer', 'exists:villes,id'],
            'commune_id'           => ['nullable', 'required_without:territoire_id', 'integer', 'exists:communes,id'],

            'adresse'              => ['nullable', 'string'],
            'telephone'            => ['nullable', 'string', 'max:50'],
            'email'                => ['nullable', 'email', 'max:255'],
            'date_creation'        => ['nullable', 'date'],

            'secretaire'           => ['nullable', 'string', 'max:255'],
            'tresorier'            => ['nullable', 'string', 'max:255'],
            'commissaire'          => ['nullable', 'string', 'max:255'],

            'membres_total'        => ['nullable', 'integer', 'min:0'],
            'superficie_totale'    => ['nullable', 'numeric', 'min:0'],
            'cultures_principales' => ['nullable', 'array'],
            'services'             => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'territoire_id.required_without' => "Le territoire est requis hors Kinshasa (sinon fournir une commune).",
            'commune_id.required_without'    => "La commune est requise pour Kinshasa (sinon fournir un territoire).",
        ];
    }
}
