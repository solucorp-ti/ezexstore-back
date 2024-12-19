<?php

namespace App\Http\Requests\Api\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class StoreTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'subdomain' => ['required', 'string', 'max:255', 'unique:tenants,subdomain'],
            'config' => ['required', 'array'],
            'config.logo_url' => ['nullable', 'string', 'url'],
            'config.company_name' => ['nullable', 'string', 'max:255'],
            'config.company_email' => ['nullable', 'email'],
            'config.whatsapp_number' => ['nullable', 'string', 'max:20'],
            'config.search_engine_type' => ['required', 'string', 'in:regular,expandable'],
        ];
    }
}