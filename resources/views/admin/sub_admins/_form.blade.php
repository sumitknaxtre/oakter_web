@php
  $selectedPermissions = old('permissions', $subAdmin->admin_permissions ?? []);
@endphp

<div class="admin-field">
  <label for="first_name">First name</label>
  <input id="first_name" type="text" name="first_name" value="{{ old('first_name', $subAdmin->first_name) }}" required />
  @error('first_name')<p class="admin-error">{{ $message }}</p>@enderror
</div>

<div class="admin-field">
  <label for="last_name">Last name</label>
  <input id="last_name" type="text" name="last_name" value="{{ old('last_name', $subAdmin->last_name) }}" required />
  @error('last_name')<p class="admin-error">{{ $message }}</p>@enderror
</div>

<div class="admin-field">
  <label for="email">Email</label>
  <input id="email" type="email" name="email" value="{{ old('email', $subAdmin->email) }}" required />
  @error('email')<p class="admin-error">{{ $message }}</p>@enderror
</div>

<div class="admin-field">
  <label for="password">Password</label>
  <input
    id="password"
    type="password"
    name="password"
    @unless ($subAdmin->exists) required @endunless
    autocomplete="new-password"
  />
  @if ($subAdmin->exists)
    <p class="admin-muted">Leave blank to keep the current password.</p>
  @endif
  @error('password')<p class="admin-error">{{ $message }}</p>@enderror
</div>

<div class="admin-field">
  <span class="admin-field-label">Sidebar permissions</span>
  <p class="admin-muted">Choose which admin sections this sub admin can access.</p>
  <div class="admin-checkbox-grid">
    @foreach ($permissionOptions as $key => $label)
      <label class="admin-checkbox">
        <input
          type="checkbox"
          name="permissions[]"
          value="{{ $key }}"
          @checked(in_array($key, $selectedPermissions, true))
        />
        <span>{{ $label }}</span>
      </label>
    @endforeach
  </div>
  @error('permissions')<p class="admin-error">{{ $message }}</p>@enderror
  @error('permissions.*')<p class="admin-error">{{ $message }}</p>@enderror
</div>
