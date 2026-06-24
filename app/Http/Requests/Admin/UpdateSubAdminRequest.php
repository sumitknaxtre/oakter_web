<?php

namespace App\Http\Requests\Admin;

use App\Support\AdminPermissions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSubAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $subAdmin = $this->route('sub_admin');

        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($subAdmin?->id),
            ],
            'password' => ['nullable', 'string', 'min:8', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in(AdminPermissions::keys())],
        ];
    }
}
