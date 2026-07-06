@extends('layouts.admin')

@section('title', 'Settings | Oakter Admin')

@section('content')
  <div class="admin-topbar">
    <div>
      <h1>Settings</h1>
      <p>Manage store-wide configuration shown on the website.</p>
    </div>
  </div>

  <section class="admin-panel" style="padding: 20px; max-width: 560px;">
    <form class="admin-form" method="post" action="{{ route('admin.settings.update') }}">
      @csrf
      @method('PUT')

      <div class="admin-field">
        <label for="shipping_days_estimate">Shipping days estimate</label>
        <input
          id="shipping_days_estimate"
          type="text"
          name="shipping_days_estimate"
          value="{{ old('shipping_days_estimate', $shippingDaysEstimate) }}"
          placeholder="10 to 14"
          required
        />
        <p class="admin-field-hint">Shown on checkout as “{{ old('shipping_days_estimate', $shippingDaysEstimate) }} working days”.</p>
        @error('shipping_days_estimate')<p class="admin-error">{{ $message }}</p>@enderror
      </div>

      <button class="admin-button" type="submit">Save settings</button>
    </form>
  </section>
@endsection
