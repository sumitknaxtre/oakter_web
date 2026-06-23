<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'price' => ['required', 'numeric', 'min:1'],
            'mrp' => ['required', 'numeric', 'min:1', 'gte:price'],
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
