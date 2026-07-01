<?php

namespace App\Http\Requests\Admin;

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
        $product = $this->route('product');

        return [
            'price' => ['required', 'numeric', 'min:1'],
            'mrp' => ['required', 'numeric', 'min:1', 'gte:price'],
            'sku' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('products', 'sku')->ignore($product?->id),
            ],
            'package_weight_kg' => ['required', 'numeric', 'min:0.01', 'max:999'],
            'package_length_cm' => ['required', 'numeric', 'min:0.5', 'max:999'],
            'package_breadth_cm' => ['required', 'numeric', 'min:0.5', 'max:999'],
            'package_height_cm' => ['required', 'numeric', 'min:0.5', 'max:999'],
            'is_in_stock' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'mrp.gte' => 'MRP must be greater than or equal to the selling price.',
        ];
    }
}
