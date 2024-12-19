<?php

namespace App\Http\Requests\Api\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'subdomain' => [
                'sometimes', 
                'string', 
                'max:255',
                Rule::unique('tenants')->ignore($this->route('tenant'))
            ],
            'config' => ['sometimes', 'array'],
            'config.logo_url' => ['nullable', 'string', 'url'],
            'config.company_name' => ['nullable', 'string', 'max:255'],
            'config.company_email' => ['nullable', 'email'],
            'config.whatsapp_number' => ['nullable', 'string', 'max:20'],
            'config.search_engine_type' => ['sometimes', 'string', 'in:regular,expandable'],
        ];
    }
}