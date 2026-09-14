<?php

namespace App\Http\Requests\Admin;

use App\Models\Dealer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDealerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:5000'],
            'phone' => ['nullable', 'string', 'max:50'],
            'map_url' => ['nullable', 'url', 'max:2048'],
            'state' => ['required', 'string', Rule::in(Dealer::adminRegionOptions())],
            'district' => ['required', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:99999'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'sort_order' => $this->filled('sort_order') ? (int) $this->input('sort_order') : 0,
            'phone' => $this->filled('phone') ? trim((string) $this->input('phone')) : null,
            'map_url' => $this->filled('map_url') ? trim((string) $this->input('map_url')) : null,
        ]);
    }
}
