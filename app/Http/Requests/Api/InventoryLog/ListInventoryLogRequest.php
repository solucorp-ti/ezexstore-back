<?php

namespace App\Http\Requests\Api\InventoryLog;

use Illuminate\Foundation\Http\FormRequest;

class ListInventoryLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'per_page' => 'nullable|integer|min:1|max:100',
            'product_id' => 'nullable|integer|exists:products,id',
            'warehouse_id' => 'nullable|integer|exists:warehouses,id',
            'type' => 'nullable|in:order,restock',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from'
        ];
    }
}