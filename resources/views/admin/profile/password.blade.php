@extends('layouts.admin')

@section('title', 'Change password | Oakter Admin')

@section('content')
  <div class="admin-topbar">
    <div>
      <h1>Change password</h1>
      <p>Keep your admin account secure with a strong password.</p>
    </div>
  </div>

  <section class="admin-panel" style="padding: 20px;">
    <form class="admin-form" method="post" action="{{ route('admin.profile.password.update') }}">
      @csrf
      @method('PUT')

      <div class="admin-field">
        <label for="current_password">Current password</label>
        <input id="current_password" type="password" name="current_password" required />
        @error('current_password')<p class="admin-error">{{ $message }}</p>@enderror
      </div>

      <div class="admin-field">
        <label for="password">New password</label>
        <input id="password" type="password" name="password" required />
        @error('password')<p class="admin-error">{{ $message }}</p>@enderror
      </div>

      <div class="admin-field">
        <label for="password_confirmation">Confirm new password</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required />
      </div>

      <button class="admin-button" type="submit">Update password</button>
    </form>
  </section>
@endsection
