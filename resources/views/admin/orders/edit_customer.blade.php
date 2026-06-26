@extends('layouts.admin')

@section('title', 'Edit customer | Order #'.$order->id)

@section('content')
  <div class="admin-topbar">
    <div>
      <h1>Edit customer</h1>
      <p>Order #{{ $order->id }} · {{ $order->product_name }}</p>
    </div>
    <a class="admin-link-button secondary" href="{{ $returnUrl }}">Back</a>
  </div>

  <section class="admin-panel" style="padding: 20px;">
    <form class="admin-form admin-form-wide" method="post" action="{{ route('admin.orders.customer.update', $order) }}">
      @csrf
      @method('PUT')
      @include('admin.orders._customer_form', ['form' => $form])
      <button class="admin-button" type="submit">Save customer details</button>
    </form>
  </section>
@endsection
