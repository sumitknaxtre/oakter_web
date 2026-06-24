@extends('layouts.admin')

@section('title', 'Edit sub admin | Oakter Admin')

@section('content')
  <div class="admin-topbar">
    <div>
      <h1>Edit sub admin</h1>
      <p>{{ $subAdmin->name }}</p>
    </div>
    <a class="admin-link-button secondary" href="{{ route('admin.sub-admins.index') }}">Back to sub admins</a>
  </div>

  <section class="admin-panel" style="padding: 20px;">
    <form class="admin-form" method="post" action="{{ route('admin.sub-admins.update', $subAdmin) }}">
      @csrf
      @method('PUT')
      @include('admin.sub_admins._form')
      <button class="admin-button" type="submit">Save sub admin</button>
    </form>
  </section>
@endsection
