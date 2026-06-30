@extends('layouts.admin')

@section('title', 'Add coupon | Oakter Admin')

@section('content')
  <div class="admin-topbar">
    <div>
      <h1>Add coupon</h1>
      <p>Create a flat discount code for one or more products.</p>
    </div>
    <a class="admin-link-button secondary" href="{{ route('admin.coupons.index') }}">Back to coupons</a>
  </div>

  <section class="admin-panel" style="padding: 20px;">
    <form class="admin-form admin-form-wide" method="post" action="{{ route('admin.coupons.store') }}">
      @csrf
      @include('admin.coupons._form')
      <button class="admin-button" type="submit">Create coupon</button>
    </form>
  </section>
@endsection
