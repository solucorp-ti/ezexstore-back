<?php

namespace App\Http\Requests\Api\V1\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // La autorización se maneja en el middleware
    }

    public function rules(): array
    {
        return [
            'product_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['active', 'inactive', 'discontinued'])],
            'unit_of_measure' => ['required', Rule::in(['piece', 'kg', 'liter', 'meter'])],
            'sku' => ['nullable', 'string', 'max:255', 'unique:products'],
            'part_number' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['required', 'string', 'max:255'],
            'part_condition' => ['nullable', Rule::in(['new', 'used', 'discontinued', 'damaged', 'refurbished'])],
            'brand' => ['nullable', 'string', 'max:255'],
            'family' => ['nullable', 'string', 'max:255'],
            'line' => ['nullable', 'string', 'max:255'],
            'photos.*' => ['nullable', 'image', 'max:2048'] // 2MB max por foto
        ];
    }

    public function messages(): array
    {
        return [
            'product_name.required' => 'El nombre del producto es requerido',
            'product_name.string' => 'El nombre debe ser texto',
            'product_name.max' => 'El nombre no debe exceder 255 caracteres',

            'base_price.required' => 'El precio base es requerido',
            'base_price.numeric' => 'El precio debe ser un número',
            'base_price.min' => 'El precio no puede ser negativo',

            'status.required' => 'El estado es requerido',
            'status.in' => 'El estado seleccionado no es válido',

            'unit_of_measure.required' => 'La unidad de medida es requerida',
            'unit_of_measure.in' => 'La unidad de medida seleccionada no es válida',

            'sku.unique' => 'Este SKU ya está en uso',

            'part_condition.in' => 'La condición seleccionada no es válida',

            'photos.*.image' => 'El archivo debe ser una imagen',
            'photos.*.max' => 'La imagen no debe pesar más de 2MB'
        ];
    }
}
