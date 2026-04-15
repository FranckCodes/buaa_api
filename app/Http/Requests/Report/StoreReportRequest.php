<?php

namespace App\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'client_id'      => ['required', 'integer', 'exists:clients,id'],
            'superviseur_id' => ['nullable', 'integer', 'exists:users,id'],
            'report_type_id' => ['required', 'integer', 'exists:report_types,id'],
            'summary'        => ['nullable', 'string', 'max:300'],
            'value_numeric'  => ['nullable', 'numeric'],
            'value_unit'     => ['nullable', 'string', 'max:50'],
            'value_text'     => ['nullable', 'string', 'max:255'],
            'details'        => ['nullable', 'string'],
            'date_rapport'   => ['required', 'date'],
        ];
    }
}
