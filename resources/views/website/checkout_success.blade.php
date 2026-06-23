@extends('layouts.checkout')

@section('title', 'Order confirmed | Oakter')
@section('meta_description', 'Your Oakter order has been placed successfully.')

@section('content')
  <section class="checkout-success">
    <div class="checkout-success-card">
      <p class="eyebrow">Order confirmed</p>
      <h1>Thank you for your purchase.</h1>
      <p>Your payment for <strong>{{ $order->product_name }}</strong> was successful.</p>
      <dl class="checkout-success-meta">
        <div>
          <dt>Order ID</dt>
          <dd>#{{ $order->id }}</dd>
        </div>
        <div>
          <dt>Amount paid</dt>
          <dd>{{ $order->formattedAmount() }}</dd>
        </div>
        @if ($order->discount_paise > 0)
          <div>
            <dt>Discount</dt>
            <dd>-{{ $order->formattedDiscount() }} ({{ $order->coupon_code }})</dd>
          </div>
        @endif
        <div>
          <dt>Payment reference</dt>
          <dd>{{ $order->razorpay_payment_id }}</dd>
        </div>
      </dl>
      <p class="checkout-muted">
        A confirmation will be sent to <strong>{{ $order->user?->email }}</strong>.
        Our team will contact you on <strong>{{ $order->phone }}</strong> for delivery updates.
      </p>
      <a class="button primary" href="{{ route('website.home') }}">Continue shopping</a>
    </div>
  </section>
@endsection
