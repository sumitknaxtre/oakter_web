@extends('layouts.admin')

@section('title', 'Profile | Oakter Admin')

@section('content')
  <div class="admin-topbar">
    <div>
      <h1>Profile settings</h1>
      <p>Update your admin account details.</p>
    </div>
  </div>

  <section class="admin-panel" style="padding: 20px;">
    <form class="admin-form" method="post" action="{{ route('admin.profile.update') }}">
      @csrf
      @method('PUT')

      <div class="admin-field">
        <label for="first_name">First name</label>
        <input id="first_name" type="text" name="first_name" value="{{ old('first_name', $admin->first_name) }}" required />
        @error('first_name')<p class="admin-error">{{ $message }}</p>@enderror
      </div>

      <div class="admin-field">
        <label for="last_name">Last name</label>
        <input id="last_name" type="text" name="last_name" value="{{ old('last_name', $admin->last_name) }}" required />
        @error('last_name')<p class="admin-error">{{ $message }}</p>@enderror
      </div>

      <div class="admin-field">
        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email', $admin->email) }}" required />
        @error('email')<p class="admin-error">{{ $message }}</p>@enderror
      </div>

      <button class="admin-button" type="submit">Save profile</button>
    </form>
  </section>
@endsection
