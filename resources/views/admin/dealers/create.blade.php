@extends('layouts.admin')

@section('title', 'Add dealer | Oakter Admin')

@section('content')
  <div class="admin-topbar">
    <div>
      <h1>Add dealer</h1>
      <p>Create a retail outlet for the Find a store page.</p>
    </div>
    <a class="admin-link-button secondary" href="{{ route('admin.dealers.index') }}">Back to dealers</a>
  </div>

  <section class="admin-panel" style="padding: 20px;">
    <form class="admin-form admin-form-wide" method="post" action="{{ route('admin.dealers.store') }}">
      @csrf
      @include('admin.dealers._form')
      <button class="admin-button" type="submit">Create dealer</button>
    </form>
  </section>
@endsection
