<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $order = $this->route('order');

        return [
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($order?->user_id),
            ],
            'phone' => ['required', 'string', 'regex:/^[6-9]\d{9}$/'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'address_line1' => ['required', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'pincode' => ['required', 'string', 'regex:/^\d{6}$/'],
            'country' => ['required', 'string', 'max:100'],
            'billing_same_as_shipping' => ['sometimes', 'boolean'],
            'billing_first_name' => ['nullable', 'required_if:billing_same_as_shipping,false', 'string', 'max:100'],
            'billing_last_name' => ['nullable', 'required_if:billing_same_as_shipping,false', 'string', 'max:100'],
            'billing_address_line1' => ['nullable', 'required_if:billing_same_as_shipping,false', 'string', 'max:255'],
            'billing_address_line2' => ['nullable', 'string', 'max:255'],
            'billing_city' => ['nullable', 'required_if:billing_same_as_shipping,false', 'string', 'max:100'],
            'billing_state' => ['nullable', 'required_if:billing_same_as_shipping,false', 'string', 'max:100'],
            'billing_pincode' => ['nullable', 'required_if:billing_same_as_shipping,false', 'string', 'regex:/^\d{6}$/'],
            'billing_country' => ['nullable', 'required_if:billing_same_as_shipping,false', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Enter a valid 10-digit Indian mobile number.',
            'pincode.regex' => 'Enter a valid 6-digit PIN code.',
            'billing_pincode.regex' => 'Enter a valid 6-digit billing PIN code.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'billing_same_as_shipping' => $this->boolean('billing_same_as_shipping'),
        ]);
    }
}
