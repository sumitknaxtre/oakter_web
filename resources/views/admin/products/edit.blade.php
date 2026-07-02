@extends('layouts.admin')

@section('title', 'Edit product | Oakter Admin')

@section('content')
  <div class="admin-topbar">
    <div>
      <h1>Edit product</h1>
      <p>{{ $product->name }}</p>
    </div>
    <a class="admin-link-button secondary" href="{{ route('admin.products.index') }}">Back to products</a>
  </div>

  <section class="admin-panel" style="padding: 20px;">
    <form class="admin-form" method="post" action="{{ route('admin.products.update', $product) }}">
      @csrf
      @method('PUT')

      <div class="admin-field">
        <label for="sku">Unicommerce SKU</label>
        <input
          id="sku"
          type="text"
          name="sku"
          value="{{ old('sku', $product->sku) }}"
          maxlength="100"
        />
        <p class="admin-muted">Must match the Uniware catalog SKU code (e.g. 27704), not the channel listing title.</p>
        @error('sku')<p class="admin-error">{{ $message }}</p>@enderror
      </div>

      <div class="admin-field">
        <label for="price">Selling price (₹)</label>
        <input
          id="price"
          type="number"
          name="price"
          min="1"
          step="0.01"
          value="{{ old('price', $product->priceInRupees()) }}"
          required
        />
        <p class="admin-muted">Used at checkout. Current Razorpay amount: {{ $product->formattedAmount() }}</p>
        @error('price')<p class="admin-error">{{ $message }}</p>@enderror
      </div>

      <div class="admin-field">
        <label for="mrp">MRP (₹)</label>
        <input
          id="mrp"
          type="number"
          name="mrp"
          min="1"
          step="0.01"
          value="{{ old('mrp', $product->mrpInRupees()) }}"
          required
        />
        <p class="admin-muted">Shown on product listing pages. Current display: {{ $product->listingMrp() ?? '—' }}</p>
        @error('mrp')<p class="admin-error">{{ $message }}</p>@enderror
      </div>

      <div class="admin-field">
        <label class="admin-checkbox">
          <input
            type="checkbox"
            name="is_in_stock"
            value="1"
            @checked(old('is_in_stock', $product->is_in_stock))
          />
          <span>In stock on website</span>
        </label>
        <p class="admin-muted">When unchecked, the buy button shows as “Out of Stock” and checkout is blocked.</p>
        @error('is_in_stock')<p class="admin-error">{{ $message }}</p>@enderror
      </div>

      <div class="admin-field">
        <label class="admin-checkbox">
          <input
            type="checkbox"
            name="hide_buy_button"
            value="1"
            @checked(old('hide_buy_button', $product->hide_buy_button))
          />
          <span>Hide buy button</span>
        </label>
        <p class="admin-muted">When checked, the buy button is removed from product pages. No out-of-stock message is shown.</p>
        @error('hide_buy_button')<p class="admin-error">{{ $message }}</p>@enderror
      </div>

      <x-admin.form-section title="Shiprocket package details">
        <p class="admin-muted" style="margin-bottom: 16px;">
          Dead weight and box dimensions sent to Shiprocket when an order is created.
          Volumetric and applied weight are calculated by Shiprocket automatically.
        </p>

        <x-admin.form-row :cols="2">
          <div class="admin-field">
            <label for="package_weight_kg">Dead weight (kg)</label>
            <input
              id="package_weight_kg"
              type="number"
              name="package_weight_kg"
              min="0.01"
              step="0.001"
              value="{{ old('package_weight_kg', $product->package_weight_kg) }}"
              required
            />
            @error('package_weight_kg')<p class="admin-error">{{ $message }}</p>@enderror
          </div>

          <div class="admin-field">
            <label for="package_length_cm">Length (cm)</label>
            <input
              id="package_length_cm"
              type="number"
              name="package_length_cm"
              min="0.5"
              step="0.01"
              value="{{ old('package_length_cm', $product->package_length_cm) }}"
              required
            />
            @error('package_length_cm')<p class="admin-error">{{ $message }}</p>@enderror
          </div>

          <div class="admin-field">
            <label for="package_breadth_cm">Breadth (cm)</label>
            <input
              id="package_breadth_cm"
              type="number"
              name="package_breadth_cm"
              min="0.5"
              step="0.01"
              value="{{ old('package_breadth_cm', $product->package_breadth_cm) }}"
              required
            />
            @error('package_breadth_cm')<p class="admin-error">{{ $message }}</p>@enderror
          </div>

          <div class="admin-field">
            <label for="package_height_cm">Height (cm)</label>
            <input
              id="package_height_cm"
              type="number"
              name="package_height_cm"
              min="0.5"
              step="0.01"
              value="{{ old('package_height_cm', $product->package_height_cm) }}"
              required
            />
            @error('package_height_cm')<p class="admin-error">{{ $message }}</p>@enderror
          </div>
        </x-admin.form-row>
      </x-admin.form-section>

      <button class="admin-button" type="submit">Save product</button>
    </form>
  </section>
@endsection
