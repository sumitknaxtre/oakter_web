@extends('layouts.admin')

@section('title', ($order->order_number ?? 'Shopify order').' | Oakter Admin')

@section('content')
  <div class="admin-topbar">
    <div>
      <h1>{{ $order->order_number ?? 'Shopify order' }}</h1>
      <p>Shopify archive order{{ $order->shopify_id ? ' #'.$order->shopify_id : '' }}</p>
    </div>
    <a class="admin-link-button secondary" href="{{ route('admin.shopify-orders.index') }}">Back to Shopify orders</a>
  </div>

  <section class="admin-panel">
    <div class="admin-panel-head">
      <h2>Order details</h2>
    </div>
    <div class="admin-detail-grid">
      <div>
        <h4 class="admin-detail-subheading">Order</h4>
        <dl class="admin-detail-list">
          <div><dt>Id</dt><dd>{{ $order->order_number ?? '—' }} @if($order->shopify_id)<span class="admin-muted">({{ $order->shopify_id }})</span>@endif</dd></div>
          <div>
            <dt>Email</dt>
            <dd>
              @if ($order->customer)
                <a href="{{ route('admin.shopify-customers.show', $order->customer) }}">{{ $order->email }}</a>
              @else
                {{ $order->email ?? '—' }}
              @endif
            </dd>
          </div>
          <div><dt>Financial Status</dt><dd>{{ $order->financial_status ?? '—' }}</dd></div>
          <div><dt>Paid At</dt><dd>{{ $order->paid_at?->format('d M Y, h:i A') ?? '—' }}</dd></div>
          <div><dt>Fulfillment Status</dt><dd>{{ $order->fulfillment_status ?? '—' }}</dd></div>
          <div><dt>Created at</dt><dd>{{ $order->shopify_created_at?->format('d M Y, h:i A') ?? '—' }}</dd></div>
          <div><dt>Cancelled at</dt><dd>{{ $order->cancelled_at?->format('d M Y, h:i A') ?? '—' }}</dd></div>
        </dl>
      </div>

      <div>
        <h4 class="admin-detail-subheading">Amounts</h4>
        <dl class="admin-detail-list">
          <div><dt>Sub total</dt><dd>{{ $order->formattedMoney($order->subtotal) }}</dd></div>
          <div><dt>Taxes</dt><dd>{{ $order->formattedMoney($order->taxes) }}</dd></div>
          <div><dt>Tax value</dt><dd>{{ $order->formattedMoney($order->tax_value) }}</dd></div>
          <div><dt>Total</dt><dd>{{ $order->formattedMoney($order->total) }}</dd></div>
          <div><dt>Discount code</dt><dd>{{ $order->discount_code ?? '—' }}</dd></div>
          <div><dt>Discount amount</dt><dd>{{ $order->formattedMoney($order->discount_amount) }}</dd></div>
          <div><dt>Refunded amount</dt><dd>{{ $order->formattedMoney($order->refunded_amount) }}</dd></div>
        </dl>
      </div>

      <div>
        <h4 class="admin-detail-subheading">Line item</h4>
        <dl class="admin-detail-list">
          <div><dt>Name</dt><dd>{{ $order->lineitem_name ?? '—' }}</dd></div>
          <div><dt>Qty</dt><dd>{{ $order->lineitem_qty ?? '—' }}</dd></div>
          <div><dt>Price</dt><dd>{{ $order->formattedMoney($order->lineitem_price) }}</dd></div>
          <div><dt>Compare at price</dt><dd>{{ $order->formattedMoney($order->lineitem_compare_at_price) }}</dd></div>
        </dl>
      </div>

      <div>
        <h4 class="admin-detail-subheading">Payment</h4>
        <dl class="admin-detail-list">
          <div><dt>Payment method</dt><dd>{{ $order->payment_method ?? '—' }}</dd></div>
          <div><dt>Payment reference</dt><dd>{{ $order->payment_reference ?? '—' }}</dd></div>
        </dl>
      </div>

      <div>
        <h4 class="admin-detail-subheading">Shipping address</h4>
        <p>{{ $order->shipping_address ?? '—' }}</p>
      </div>

      <div>
        <h4 class="admin-detail-subheading">Billing address</h4>
        <p>{{ $order->billing_address ?? '—' }}</p>
      </div>

      <div style="grid-column: 1 / -1;">
        <h4 class="admin-detail-subheading">Note attributes</h4>
        <pre style="white-space: pre-wrap; margin: 0; font-family: inherit;">{{ $order->note_attributes ?: '—' }}</pre>
      </div>
    </div>
  </section>
@endsection
