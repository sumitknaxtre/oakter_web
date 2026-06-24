@extends('layouts.admin')

@section('title', 'Add sub admin | Oakter Admin')

@section('content')
  <div class="admin-topbar">
    <div>
      <h1>Add sub admin</h1>
      <p>Create a sub admin account with selected sidebar permissions.</p>
    </div>
    <a class="admin-link-button secondary" href="{{ route('admin.sub-admins.index') }}">Back to sub admins</a>
  </div>

  <section class="admin-panel" style="padding: 20px;">
    <form class="admin-form" method="post" action="{{ route('admin.sub-admins.store') }}">
      @csrf
      @include('admin.sub_admins._form')
      <button class="admin-button" type="submit">Create sub admin</button>
    </form>
  </section>
@endsection
