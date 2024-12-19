<?php

namespace App\Http\Requests\Api\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductPhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Ajustar según lógica de autorización
    }

    public function rules(): array
    {
        return [
            'photos' => ['required', 'array', 'min:1'],
            'photos.*' => ['required', 'image', 'max:2048'] // 2MB máximo por foto
        ];
    }
}