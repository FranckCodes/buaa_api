<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClientProfileRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            // Identité
            'date_naissance'           => ['nullable', 'date'],
            'lieu_naissance'           => ['nullable', 'string', 'max:100'],
            'sexe'                     => ['nullable', Rule::in(['M', 'F'])],
            'etat_civil'               => ['nullable', Rule::in(['celibataire', 'marie', 'divorce', 'veuf'])],

            'nationalite'             => ['nullable', 'string', 'max:100'],

            // Localisation
            'adresse_complete'         => ['nullable', 'string'],
            'province_id'              => ['nullable', 'integer', 'exists:provinces,id'],
            'territoire_id'            => ['nullable', 'integer', 'exists:territoires,id'],
            'secteur_id'               => ['nullable', 'integer', 'exists:secteurs,id'],
            'ville_id'                 => ['nullable', 'integer', 'exists:villes,id'],
            'commune_id'               => ['nullable', 'integer', 'exists:communes,id'],

            // Activité
            'client_activity_type_id'  => ['nullable', 'integer', 'exists:client_activity_types,id'],
            'client_structure_type_id' => ['nullable', 'integer', 'exists:client_structure_types,id'],
            'profession_detaillee'     => ['nullable', 'string', 'max:255'],
            'experience_annees'        => ['nullable', 'integer', 'min:0'],
            'superficie_exploitation'  => ['nullable', 'numeric', 'min:0'],
            'type_culture'             => ['nullable', 'string', 'max:255'],
            'nombre_animaux'           => ['nullable', 'integer', 'min:0'],

            // Finances
            'revenus_mensuels'         => ['nullable', 'numeric', 'min:0'],
            'autres_sources_revenus'   => ['nullable', 'string'],
            'banque_principale'        => ['nullable', 'string', 'max:100'],
            'numero_compte'            => ['nullable', 'string', 'max:100'],

            // Référence / garant
            'ref_nom'                  => ['nullable', 'string', 'max:100'],
            'ref_telephone'            => ['nullable', 'string', 'max:30'],
            'ref_relation'             => ['nullable', 'string', 'max:100'],

            // Assignation
            'superviseur_id'           => ['nullable', 'string', 'exists:users,id'],
        ];
    }
}
