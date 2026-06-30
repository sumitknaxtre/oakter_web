@extends('layouts.admin')

@section('title', 'Edit coupon | Oakter Admin')

@section('content')
  <div class="admin-topbar">
    <div>
      <h1>Edit coupon</h1>
      <p>Update {{ $coupon->code }}.</p>
    </div>
    <a class="admin-link-button secondary" href="{{ route('admin.coupons.index') }}">Back to coupons</a>
  </div>

  <section class="admin-panel" style="padding: 20px;">
    <form class="admin-form admin-form-wide" method="post" action="{{ route('admin.coupons.update', $coupon) }}">
      @csrf
      @method('PUT')
      @include('admin.coupons._form')
      <button class="admin-button" type="submit">Save coupon</button>
    </form>
  </section>
@endsection
