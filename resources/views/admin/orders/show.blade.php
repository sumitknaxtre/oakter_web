@extends('layouts.admin')

@section('title', 'Order #'.$order->id.' | Oakter Admin')

@section('content')
  @php
    $shipping = $order->shipping_snapshot ?? [];
    $billing = $order->billing_snapshot ?? [];
  @endphp

  <div class="admin-topbar">
    <div>
      <h1>Order #{{ $order->id }}</h1>
      <p>{{ $order->product_name }}</p>
    </div>
    <a class="admin-link-button secondary" href="{{ route('admin.orders.index') }}">Back to orders</a>
  </div>

  <section class="admin-panel">
    <div class="admin-panel-head">
      <h2>Order summary</h2>
    </div>

    <div class="admin-detail-grid">
      <article>
        <h3>Customer</h3>
        <p><strong>Name:</strong> {{ $order->customer_name }}</p>
        <p><strong>Email:</strong> {{ $order->user?->email ?? '—' }}</p>
        <p><strong>Phone:</strong> {{ $order->phone ?? '—' }}</p>
      </article>

      <article>
        <h3>Payment</h3>
        <p><strong>Subtotal:</strong> {{ $order->formattedSubtotal() }}</p>
        @if ($order->discount_paise > 0)
          <p><strong>Discount:</strong> -{{ $order->formattedDiscount() }} ({{ $order->coupon_code }})</p>
        @endif
        <p><strong>Amount:</strong> {{ $order->formattedAmount() }}</p>
        <p><strong>Razorpay order:</strong> {{ $order->razorpay_order_id ?? '—' }}</p>
        <p><strong>Payment ID:</strong> {{ $order->razorpay_payment_id ?? '—' }}</p>
        <p><strong>Paid at:</strong> {{ $order->paid_at?->format('d M Y, h:i A') ?? '—' }}</p>
      </article>

      <article>
        <h3>Shipping address</h3>
        <p>{{ $shipping['address_line1'] ?? '—' }}</p>
        @if (! empty($shipping['address_line2']))<p>{{ $shipping['address_line2'] }}</p>@endif
        <p>{{ $shipping['city'] ?? '' }}, {{ $shipping['state'] ?? '' }} {{ $shipping['pincode'] ?? '' }}</p>
        <p>{{ $shipping['country'] ?? '' }}</p>
      </article>

      <article>
        <h3>Billing address</h3>
        @if ($order->billing_same_as_shipping)
          <p>Same as shipping address</p>
        @else
          <p>{{ $billing['address_line1'] ?? '—' }}</p>
          @if (! empty($billing['address_line2']))<p>{{ $billing['address_line2'] }}</p>@endif
          <p>{{ $billing['city'] ?? '' }}, {{ $billing['state'] ?? '' }} {{ $billing['pincode'] ?? '' }}</p>
          <p>{{ $billing['country'] ?? '' }}</p>
        @endif
      </article>
    </div>
  </section>
@endsection
