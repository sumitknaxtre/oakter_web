@extends('layouts.admin')

@section('title', ($activeTab === 'password' ? 'Change password' : 'Profile').' | Oakter Admin')

@section('content')
  <div class="admin-topbar">
    <div>
      @if ($activeTab === 'password')
        <h1>Change password</h1>
        <p>Keep your admin account secure with a strong password.</p>
      @else
        <h1>Profile settings</h1>
        <p>Update your admin account details.</p>
      @endif
    </div>
  </div>

  <div class="admin-profile-shell">
    <nav class="admin-profile-tabs" aria-label="Profile sections">
      <a
        href="{{ route('admin.profile.edit') }}"
        @class(['is-active' => $activeTab === 'profile'])
        @if ($activeTab === 'profile') aria-current="page" @endif
      >Profile</a>
      <a
        href="{{ route('admin.profile.edit', ['tab' => 'password']) }}"
        @class(['is-active' => $activeTab === 'password'])
        @if ($activeTab === 'password') aria-current="page" @endif
      >Change password</a>
    </nav>

    <section class="admin-panel admin-profile-panel">
      @if ($activeTab === 'password')
        @include('admin.profile.partials.password-form')
      @else
        @include('admin.profile.partials.profile-form')
      @endif
    </section>
  </div>
@endsection
