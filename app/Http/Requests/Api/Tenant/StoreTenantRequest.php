<?php

namespace App\Http\Requests\Api\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'subdomain' => [
                'required', 
                'string', 
                'max:63',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('tenants', 'subdomain')->whereNull('deleted_at')
            ],
            'config' => ['required', 'array'],
            'config.company_name' => ['nullable', 'string', 'max:255'],
            'config.company_email' => ['nullable', 'email'],
            'config.whatsapp_number' => ['nullable', 'string', 'max:20'],
            'config.search_engine_type' => ['required', 'string', 'in:regular,expandable'],
            'user' => ['required', 'array'],
            'user.name' => ['required', 'string', 'max:255'],
            'user.email' => ['required', 'email', Rule::unique('users', 'email')->whereNull('deleted_at')],
            'user.password' => ['required', 'string', 'min:8'],
            'user.role_id' => ['required', 'exists:roles,id']
        ];
    }

    public function messages(): array
    {
        return [
            'subdomain.unique' => 'Este subdominio ya está en uso.',
            'user.email.unique' => 'Este correo electrónico ya está registrado.',
            'user.role_id.required' => 'El rol es requerido.',
            'user.role_id.exists' => 'El rol seleccionado no existe.',
        ];
    }
}