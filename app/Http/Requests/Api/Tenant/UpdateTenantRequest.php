<?php

namespace App\Http\Requests\Api\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Ajustar según tu lógica de autorización
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
            ]
        ];
    }
}