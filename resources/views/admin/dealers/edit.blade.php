@extends('layouts.admin')

@section('title', 'Edit dealer | Oakter Admin')

@section('content')
  <div class="admin-topbar">
    <div>
      <h1>Edit dealer</h1>
      <p>{{ $dealer->name }}</p>
    </div>
    <a class="admin-link-button secondary" href="{{ route('admin.dealers.index') }}">Back to dealers</a>
  </div>

  <section class="admin-panel" style="padding: 20px;">
    <form class="admin-form admin-form-wide" method="post" action="{{ route('admin.dealers.update', $dealer) }}">
      @csrf
      @method('PUT')
      @include('admin.dealers._form')
      <button class="admin-button" type="submit">Save changes</button>
    </form>
  </section>
@endsection
