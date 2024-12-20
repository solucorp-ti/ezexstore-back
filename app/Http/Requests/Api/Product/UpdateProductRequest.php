<?php

namespace App\Http\Requests\Api\V1\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'base_price' => ['sometimes', 'numeric', 'min:0'],
            'status' => ['sometimes', Rule::in(['active', 'inactive', 'discontinued'])],
            'unit_of_measure' => ['sometimes', Rule::in(['piece', 'kg', 'liter', 'meter'])],
            'sku' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products')->ignore($this->route('product'))
            ],
            'part_number' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['sometimes', 'string', 'max:255'],
            'part_condition' => ['nullable', Rule::in(['new', 'used', 'discontinued', 'damaged', 'refurbished'])],
            'brand' => ['nullable', 'string', 'max:255'],
            'family' => ['nullable', 'string', 'max:255'],
            'line' => ['nullable', 'string', 'max:255'],
            'photos.*' => ['nullable', 'image', 'max:2048']
        ];
    }

    public function messages(): array
    {
        return [
            'product_name.string' => 'El nombre debe ser texto',
            'product_name.max' => 'El nombre no debe exceder 255 caracteres',

            'base_price.numeric' => 'El precio debe ser un número',
            'base_price.min' => 'El precio no puede ser negativo',

            'status.in' => 'El estado seleccionado no es válido',

            'unit_of_measure.in' => 'La unidad de medida seleccionada no es válida',

            'sku.unique' => 'Este SKU ya está en uso',

            'part_condition.in' => 'La condición seleccionada no es válida',

            'photos.*.image' => 'El archivo debe ser una imagen',
            'photos.*.max' => 'La imagen no debe pesar más de 2MB'
        ];
    }
}
