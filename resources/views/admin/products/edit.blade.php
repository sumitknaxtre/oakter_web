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
        <p class="admin-muted">When unchecked, the buy button is disabled and checkout is blocked.</p>
        @error('is_in_stock')<p class="admin-error">{{ $message }}</p>@enderror
      </div>

      <button class="admin-button" type="submit">Save product</button>
    </form>
  </section>
@endsection
