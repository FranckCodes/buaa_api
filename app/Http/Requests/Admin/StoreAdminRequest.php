<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAdminRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            // Compte utilisateur
            'nom'                    => ['required', 'string', 'max:100'],
            'postnom'                => ['nullable', 'string', 'max:100'],
            'prenom'                 => ['required', 'string', 'max:100'],
            'email'                  => ['required', 'email', 'max:255', 'unique:users,email'],
            'telephone'              => ['nullable', 'string', 'max:30'],
            'password'               => ['required', 'string', 'min:8', 'confirmed'],

            // Profil pro
            'matricule'              => ['nullable', 'string', 'max:50', 'unique:admins,matricule'],
            'telephone_pro'          => ['nullable', 'string', 'max:20'],
            'notes'                  => ['nullable', 'string'],
            'is_active'              => ['nullable', 'boolean'],

            // Identité
            'date_naissance'         => ['nullable', 'date'],
            'lieu_naissance'         => ['nullable', 'string', 'max:191'],
            'sexe'                   => ['nullable', Rule::in(['M', 'F'])],
            'etat_civil'             => ['nullable', Rule::in(['celibataire', 'marie', 'divorce', 'veuf'])],
            'nationalite'            => ['nullable', 'string', 'max:100'],

            // Localisation personnelle
            'adresse_complete'       => ['nullable', 'string'],
            'province_id'            => ['nullable', 'integer', 'exists:provinces,id'],
            'territoire_id'          => ['nullable', 'integer', 'exists:territoires,id'],
            'secteur_id'             => ['nullable', 'integer', 'exists:secteurs,id'],
            'ville_id'               => ['nullable', 'integer', 'exists:villes,id'],
            'commune_id'             => ['nullable', 'integer', 'exists:communes,id'],

            // Professionnel
            'niveau_etude'           => ['nullable', 'string', 'max:100'],
            'specialite'             => ['nullable', 'string', 'max:191'],
            'experience_annees'      => ['nullable', 'integer', 'min:0'],
            'type_piece_identite'    => ['nullable', 'string', 'max:50'],
            'numero_piece_identite'  => ['nullable', 'string', 'max:100'],

            // Provinces à charge
            'provinces'              => ['nullable', 'array'],
            'provinces.*'            => ['integer', 'exists:provinces,id'],
        ];
    }
}
