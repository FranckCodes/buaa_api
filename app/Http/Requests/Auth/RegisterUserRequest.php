<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nom'          => ['required', 'string', 'max:100'],
            'postnom'      => ['nullable', 'string', 'max:100'],
            'prenom'       => ['required', 'string', 'max:100'],
            'email'        => ['required', 'email', 'max:255', 'unique:users,email'],
            'telephone'    => ['nullable', 'string', 'max:30'],
            'password'     => ['required', 'string', 'min:8', 'confirmed'],
            'status_code'  => ['nullable', 'string', Rule::exists('user_statuses', 'code')],
            'role_codes'   => ['required', 'array', 'min:1'],
            'role_codes.*' => ['string', Rule::exists('roles', 'code')],
            'photo_profil' => ['nullable', 'string', 'max:500'],
        ];
    }
}
