@php
  $values = $form;
@endphp

<x-admin.form-row :cols="2">
  <div class="admin-field">
    <label for="email">Email</label>
    <input id="email" type="email" name="email" value="{{ $values['email'] }}" required />
    @error('email')<p class="admin-error">{{ $message }}</p>@enderror
  </div>

  <div class="admin-field">
    <label for="phone">Phone</label>
    <input id="phone" type="text" name="phone" value="{{ $values['phone'] }}" inputmode="numeric" maxlength="10" required />
    @error('phone')<p class="admin-error">{{ $message }}</p>@enderror
  </div>
</x-admin.form-row>

<x-admin.form-row :cols="2">
  <div class="admin-field">
    <label for="first_name">First name</label>
    <input id="first_name" type="text" name="first_name" value="{{ $values['first_name'] }}" required />
    @error('first_name')<p class="admin-error">{{ $message }}</p>@enderror
  </div>

  <div class="admin-field">
    <label for="last_name">Last name</label>
    <input id="last_name" type="text" name="last_name" value="{{ $values['last_name'] }}" required />
    @error('last_name')<p class="admin-error">{{ $message }}</p>@enderror
  </div>
</x-admin.form-row>

<x-admin.form-section title="Shipping address" />

<x-admin.form-row :cols="1">
  <div class="admin-field">
    <label for="address_line1">Address line 1</label>
    <input id="address_line1" type="text" name="address_line1" value="{{ $values['address_line1'] }}" required />
    @error('address_line1')<p class="admin-error">{{ $message }}</p>@enderror
  </div>
</x-admin.form-row>

<x-admin.form-row :cols="1">
  <div class="admin-field">
    <label for="address_line2">Address line 2 (optional)</label>
    <input id="address_line2" type="text" name="address_line2" value="{{ $values['address_line2'] }}" />
    @error('address_line2')<p class="admin-error">{{ $message }}</p>@enderror
  </div>
</x-admin.form-row>

<x-admin.form-row :cols="3">
  <div class="admin-field">
    <label for="city">City</label>
    <input id="city" type="text" name="city" value="{{ $values['city'] }}" required />
    @error('city')<p class="admin-error">{{ $message }}</p>@enderror
  </div>

  <div class="admin-field">
    <label for="state">State</label>
    <input id="state" type="text" name="state" value="{{ $values['state'] }}" required />
    @error('state')<p class="admin-error">{{ $message }}</p>@enderror
  </div>

  <div class="admin-field">
    <label for="pincode">PIN code</label>
    <input id="pincode" type="text" name="pincode" value="{{ $values['pincode'] }}" inputmode="numeric" maxlength="6" required />
    @error('pincode')<p class="admin-error">{{ $message }}</p>@enderror
  </div>
</x-admin.form-row>

<x-admin.form-row :cols="1">
  <div class="admin-field">
    <label for="country">Country</label>
    <input id="country" type="text" name="country" value="{{ $values['country'] }}" required />
    @error('country')<p class="admin-error">{{ $message }}</p>@enderror
  </div>
</x-admin.form-row>

<div class="admin-field">
  <label class="admin-checkbox">
    <input
      type="checkbox"
      name="billing_same_as_shipping"
      value="1"
      data-billing-same-toggle
      @checked($values['billing_same_as_shipping'])
    />
    <span>Billing address same as shipping</span>
  </label>
</div>

<div class="admin-billing-fields" data-billing-fields @if ($values['billing_same_as_shipping']) hidden @endif>
  <x-admin.form-section title="Billing address" />

  <x-admin.form-row :cols="2">
    <div class="admin-field">
      <label for="billing_first_name">Billing first name</label>
      <input id="billing_first_name" type="text" name="billing_first_name" value="{{ $values['billing_first_name'] }}" />
      @error('billing_first_name')<p class="admin-error">{{ $message }}</p>@enderror
    </div>

    <div class="admin-field">
      <label for="billing_last_name">Billing last name</label>
      <input id="billing_last_name" type="text" name="billing_last_name" value="{{ $values['billing_last_name'] }}" />
      @error('billing_last_name')<p class="admin-error">{{ $message }}</p>@enderror
    </div>
  </x-admin.form-row>

  <x-admin.form-row :cols="1">
    <div class="admin-field">
      <label for="billing_address_line1">Billing address line 1</label>
      <input id="billing_address_line1" type="text" name="billing_address_line1" value="{{ $values['billing_address_line1'] }}" />
      @error('billing_address_line1')<p class="admin-error">{{ $message }}</p>@enderror
    </div>
  </x-admin.form-row>

  <x-admin.form-row :cols="1">
    <div class="admin-field">
      <label for="billing_address_line2">Billing address line 2 (optional)</label>
      <input id="billing_address_line2" type="text" name="billing_address_line2" value="{{ $values['billing_address_line2'] }}" />
      @error('billing_address_line2')<p class="admin-error">{{ $message }}</p>@enderror
    </div>
  </x-admin.form-row>

  <x-admin.form-row :cols="3">
    <div class="admin-field">
      <label for="billing_city">Billing city</label>
      <input id="billing_city" type="text" name="billing_city" value="{{ $values['billing_city'] }}" />
      @error('billing_city')<p class="admin-error">{{ $message }}</p>@enderror
    </div>

    <div class="admin-field">
      <label for="billing_state">Billing state</label>
      <input id="billing_state" type="text" name="billing_state" value="{{ $values['billing_state'] }}" />
      @error('billing_state')<p class="admin-error">{{ $message }}</p>@enderror
    </div>

    <div class="admin-field">
      <label for="billing_pincode">Billing PIN code</label>
      <input id="billing_pincode" type="text" name="billing_pincode" value="{{ $values['billing_pincode'] }}" inputmode="numeric" maxlength="6" />
      @error('billing_pincode')<p class="admin-error">{{ $message }}</p>@enderror
    </div>
  </x-admin.form-row>

  <x-admin.form-row :cols="1">
    <div class="admin-field">
      <label for="billing_country">Billing country</label>
      <input id="billing_country" type="text" name="billing_country" value="{{ $values['billing_country'] }}" />
      @error('billing_country')<p class="admin-error">{{ $message }}</p>@enderror
    </div>
  </x-admin.form-row>
</div>
