<x-admin.form-section title="Coupon details" />

<x-admin.form-row :cols="2">
  <div class="admin-field">
    <label for="code">Coupon code</label>
    <input id="code" type="text" name="code" value="{{ old('code', $coupon->code) }}" required />
    @error('code')<p class="admin-error">{{ $message }}</p>@enderror
  </div>

  <div class="admin-field">
    <label for="discount_amount">Flat discount (₹)</label>
    <input id="discount_amount" type="number" name="discount_amount" min="1" step="0.01" value="{{ old('discount_amount', $coupon->exists ? $coupon->discount_amount_paise / 100 : '') }}" required />
    @error('discount_amount')<p class="admin-error">{{ $message }}</p>@enderror
  </div>
</x-admin.form-row>

<x-admin.form-toggle
  name="is_active"
  label="Active"
  hint="When enabled, customers can apply this coupon at checkout."
  :checked="old('is_active', $coupon->is_active)"
/>

{{-- <x-admin.form-section title="Validity &amp; limits" /> --}}

<x-admin.form-row :cols="2">
  <div class="admin-field">
    <label for="starts_at">Starts at (optional)</label>
    <input id="starts_at" type="datetime-local" name="starts_at" value="{{ old('starts_at', optional($coupon->starts_at)->format('Y-m-d\TH:i')) }}" />
    @error('starts_at')<p class="admin-error">{{ $message }}</p>@enderror
  </div>

  <div class="admin-field">
    <label for="ends_at">Ends at (optional)</label>
    <input id="ends_at" type="datetime-local" name="ends_at" value="{{ old('ends_at', optional($coupon->ends_at)->format('Y-m-d\TH:i')) }}" />
    @error('ends_at')<p class="admin-error">{{ $message }}</p>@enderror
  </div>
</x-admin.form-row>

<x-admin.form-row :cols="2">
  <div class="admin-field">
    <label for="usage_limit">Usage limit (optional)</label>
    <input id="usage_limit" type="number" name="usage_limit" min="1" value="{{ old('usage_limit', $coupon->usage_limit) }}" />
    @error('usage_limit')<p class="admin-error">{{ $message }}</p>@enderror
  </div>
</x-admin.form-row>

<x-admin.form-section title="Applicable products">
  <div class="admin-field">
    <div class="admin-checkbox-grid">
      @foreach ($products as $product)
        <label class="admin-checkbox">
          <input
            type="checkbox"
            name="product_ids[]"
            value="{{ $product->id }}"
            @checked(in_array($product->id, old('product_ids', $coupon->exists ? $coupon->products->pluck('id')->all() : []), true))
          />
          <span>{{ $product->name }}</span>
        </label>
      @endforeach
    </div>
    @error('product_ids')<p class="admin-error">{{ $message }}</p>@enderror
    @error('product_ids.*')<p class="admin-error">{{ $message }}</p>@enderror
  </div>
</x-admin.form-section>
