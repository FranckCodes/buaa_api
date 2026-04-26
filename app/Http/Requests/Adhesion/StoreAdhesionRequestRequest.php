<?php

namespace App\Http\Requests\Adhesion;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdhesionRequestRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nom'                      => ['required', 'string', 'max:255'],
            'demandeur_type'           => ['required', 'string', 'max:50'],

            'union_id'                 => ['required', 'string', 'max:50', 'exists:unions,id'],
            'client_id'                => ['nullable', 'string', 'max:50', 'exists:clients,id'],

            'client_activity_type_id'  => ['nullable', 'integer', 'exists:client_activity_types,id'],
            'client_structure_type_id' => ['nullable', 'integer', 'exists:client_structure_types,id'],

            'representant'             => ['nullable', 'string', 'max:255'],
            'telephone'                => ['required', 'string', 'max:50'],
            'email'                    => ['nullable', 'email', 'max:255'],
            'adresse'                  => ['nullable', 'string'],

            'province_id'              => ['required', 'integer', 'exists:provinces,id'],
            'territoire_id'            => ['nullable', 'required_without:commune_id', 'integer', 'exists:territoires,id'],
            'secteur_id'               => ['nullable', 'integer', 'exists:secteurs,id'],
            'ville_id'                 => ['nullable', 'integer', 'exists:villes,id'],
            'commune_id'               => ['nullable', 'required_without:territoire_id', 'integer', 'exists:communes,id'],

            'date_demande'             => ['nullable', 'date'],
            'cotisation'               => ['nullable', 'numeric', 'min:0'],
            'membres_nombre'           => ['nullable', 'integer', 'min:0'],
            'superficie_totale'        => ['nullable', 'numeric', 'min:0'],
            'type_culture'             => ['nullable', 'string', 'max:255'],
            'experience_annees'        => ['nullable', 'integer', 'min:0'],
            'nombre_animaux'           => ['nullable', 'integer', 'min:0'],
            'type_elevage'             => ['nullable', 'string', 'max:255'],
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
