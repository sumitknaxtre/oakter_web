<x-admin.form-section title="Dealer details" />

<x-admin.form-row :cols="2">
  <div class="admin-field">
    <label for="name">Store name</label>
    <input id="name" type="text" name="name" value="{{ old('name', $dealer->name) }}" required />
    @error('name')<p class="admin-error">{{ $message }}</p>@enderror
  </div>

  <div class="admin-field">
    <label for="district">District</label>
    <input id="district" type="text" name="district" value="{{ old('district', $dealer->district) }}" required />
    @error('district')<p class="admin-error">{{ $message }}</p>@enderror
  </div>
</x-admin.form-row>

<div class="admin-field">
  <label for="address">Address</label>
  <textarea id="address" name="address" rows="4" required>{{ old('address', $dealer->address) }}</textarea>
  @error('address')<p class="admin-error">{{ $message }}</p>@enderror
</div>

<x-admin.form-row :cols="2">
  <div class="admin-field">
    <label for="state">Territory / region</label>
    <select id="state" name="state" required>
      <option value="">Select territory</option>
      @foreach ($states as $state)
        <option value="{{ $state }}" @selected(old('state', $dealer->state) === $state)>{{ $state }}</option>
      @endforeach
    </select>
    @error('state')<p class="admin-error">{{ $message }}</p>@enderror
  </div>

  <div class="admin-field">
    <label for="phone">Phone (optional)</label>
    <input id="phone" type="text" name="phone" value="{{ old('phone', $dealer->phone) }}" />
    @error('phone')<p class="admin-error">{{ $message }}</p>@enderror
  </div>
</x-admin.form-row>

<x-admin.form-row :cols="2">
  <div class="admin-field">
    <label for="map_url">Map URL (optional)</label>
    <input id="map_url" type="url" name="map_url" value="{{ old('map_url', $dealer->map_url) }}" placeholder="https://" />
    @error('map_url')<p class="admin-error">{{ $message }}</p>@enderror
  </div>

  <div class="admin-field">
    <label for="sort_order">Sort order</label>
    <input id="sort_order" type="number" name="sort_order" min="0" value="{{ old('sort_order', $dealer->sort_order) }}" />
    @error('sort_order')<p class="admin-error">{{ $message }}</p>@enderror
  </div>
</x-admin.form-row>

<x-admin.form-toggle
  name="is_active"
  label="Active"
  hint="When enabled, this dealer appears on the Find a store page."
  :checked="old('is_active', $dealer->is_active)"
/>
