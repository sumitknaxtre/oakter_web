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
    <div class="admin-topbar-actions">
      <a class="admin-link-button secondary" href="{{ route('admin.orders.index') }}">Back to orders</a>
      @if ($order->canBeCancelled())
        <form
          method="post"
          action="{{ route('admin.orders.cancel', $order) }}"
          onsubmit="return confirm('Cancel this order and refund {{ $order->formattedAmount() }} to the customer via Razorpay?');"
        >
          @csrf
          <button class="admin-link-button" type="submit" style="border-color:#b42318;color:#b42318;">Cancel &amp; refund</button>
        </form>
      @endif

      @if ($order->canResyncToUnicommerce())
        <form
          method="post"
          action="{{ route('admin.orders.unicommerce.resync', $order) }}"
          onsubmit="return confirm('Send this order to Uniware again?');"
        >
          @csrf
          <button class="admin-link-button" type="submit">Re-sync to Uniware</button>
        </form>
      @endif
      @if ($order->canResyncToShiprocket())
        <form
          method="post"
          action="{{ route('admin.orders.shiprocket.resync', $order) }}"
          onsubmit="return confirm('Send this order to Shiprocket again?');"
        >
          @csrf
          <button class="admin-link-button" type="submit">Re-sync to Shiprocket</button>
        </form>
      @endif
      @if (! $order->isCancelled())
        <a class="admin-link-button" href="{{ route('admin.orders.customer.edit', $order) }}">Edit customer</a>
      @endif
    </div>
  </div>

  @error('cancel')
    <div class="admin-alert" style="margin-bottom:16px;background:#fef3f2;color:#912018;">{{ $message }}</div>
  @enderror

  @error('unicommerce')
    <div class="admin-alert" style="margin-bottom:16px;background:#fef3f2;color:#912018;">{{ $message }}</div>
  @enderror

  @error('shiprocket')
    <div class="admin-alert" style="margin-bottom:16px;background:#fef3f2;color:#912018;">{{ $message }}</div>
  @enderror

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

        <h4 class="admin-detail-subheading">Shipping address</h4>
        <p>{{ $shipping['address_line1'] ?? '—' }}</p>
        @if (! empty($shipping['address_line2']))<p>{{ $shipping['address_line2'] }}</p>@endif
        <p>{{ $shipping['city'] ?? '' }}, {{ $shipping['state'] ?? '' }} {{ $shipping['pincode'] ?? '' }}</p>
        <p>{{ $shipping['country'] ?? '' }}</p>

        <h4 class="admin-detail-subheading">Billing address</h4>
        @if ($order->billing_same_as_shipping)
          <p>Same as shipping address</p>
        @else
          <p>{{ $billing['address_line1'] ?? '—' }}</p>
          @if (! empty($billing['address_line2']))<p>{{ $billing['address_line2'] }}</p>@endif
          <p>{{ $billing['city'] ?? '' }}, {{ $billing['state'] ?? '' }} {{ $billing['pincode'] ?? '' }}</p>
          <p>{{ $billing['country'] ?? '' }}</p>
        @endif
      </article>

      <article>
        <h3>Payment</h3>
        <p><strong>Subtotal:</strong> {{ $order->formattedSubtotal() }}</p>
        @if ($order->discount_paise > 0)
          <p><strong>Discount:</strong> -{{ $order->formattedDiscount() }} ({{ $order->coupon_code }})</p>
        @endif
        <p><strong>Shipping:</strong> {{ $order->formattedShippingCharges() }}</p>
        <p><strong>Tax (GST incl.):</strong> {{ $order->formattedTaxAmount() }}</p>
        <p><strong>Amount:</strong> {{ $order->formattedAmount() }}</p>
        <p><strong>Payment status:</strong> {{ $order->paymentStatusLabel() }}</p>
        <p><strong>Payment method:</strong> {{ $order->payment_method ?? '—' }}</p>
        <p><strong>Fulfillment:</strong> {{ $order->fulfillmentStatusLabel() }}</p>
        <p><strong>Unicommerce sync:</strong> {{ $order->unicommerceSyncStatusLabel() }}</p>
        <p><strong>Uniware order code:</strong> {{ $order->unicommerce_sale_order_code ?? '—' }}</p>
        @if ($order->unicommerce_synced_at)
          <p><strong>Synced at:</strong> {{ $order->unicommerce_synced_at->format('d M Y, h:i A') }}</p>
        @endif
        @if ($order->unicommerce_last_error)
          <p><strong>Uniware sync error:</strong> {{ $order->unicommerce_last_error }}</p>
        @endif
        <p><strong>Shiprocket sync:</strong> {{ $order->shiprocketSyncStatusLabel() }}</p>
        <p><strong>Shiprocket reference:</strong> {{ $order->shiprocket_reference ?? '—' }}</p>
        <p><strong>Shiprocket order ID:</strong> {{ $order->shiprocket_order_id ?? '—' }}</p>
        <p><strong>Shiprocket shipment ID:</strong> {{ $order->shiprocket_shipment_id ?? '—' }}</p>
        @if ($order->shiprocket_synced_at)
          <p><strong>Shiprocket synced at:</strong> {{ $order->shiprocket_synced_at->format('d M Y, h:i A') }}</p>
        @endif
        @if ($order->shiprocket_cancelled_at)
          <p><strong>Shiprocket cancelled at:</strong> {{ $order->shiprocket_cancelled_at->format('d M Y, h:i A') }}</p>
        @endif
        @if ($order->shiprocket_last_error)
          <p><strong>Shiprocket sync error:</strong> {{ $order->shiprocket_last_error }}</p>
        @endif
        <p><strong>Razorpay order:</strong> {{ $order->razorpay_order_id ?? '—' }}</p>
        <p><strong>Payment ID:</strong> {{ $order->razorpay_payment_id ?? '—' }}</p>
        @if ($order->razorpay_refund_id)
          <p><strong>Refund ID:</strong> {{ $order->razorpay_refund_id }}</p>
        @endif
        <p><strong>Paid at:</strong> {{ $order->paid_at?->format('d M Y, h:i A') ?? '—' }}</p>
        @if ($order->cancelled_at)
          <p class="admin-payment-status-refunded"><strong>Cancelled at:</strong> {{ $order->cancelled_at->format('d M Y, h:i A') }}</p>
        @endif
        @if ($order->refunded_at)
          <p class="admin-payment-status-refunded"><strong>Refunded at:</strong> {{ $order->refunded_at->format('d M Y, h:i A') }}</p>
        @endif
      </article>

      <article>
        <h3>Traffic source</h3>
        <p><strong>Channel:</strong> {{ $order->attributionLabel() }}</p>
        @forelse ($order->attributionDetails() as $row)
          @if ($row['label'] !== 'Channel')
            <p><strong>{{ $row['label'] }}:</strong> {{ $row['value'] }}</p>
          @endif
        @empty
          <p class="admin-muted">No attribution data captured for this order.</p>
        @endforelse
        @if ($order->meta_event_id)
          <p><strong>Meta event ID:</strong> {{ $order->meta_event_id }}</p>
        @endif
      </article>
    </div>
  </section>
@endsection
