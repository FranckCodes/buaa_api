<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'telephone' => ['required', 'string', 'max:20'],
            'code'      => ['required', 'string', 'size:6'],
        ];
    }
}
