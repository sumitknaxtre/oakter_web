@extends('layouts.admin')

@section('title', 'Shopify Orders | Oakter Admin')

@section('content')
  <div class="admin-topbar">
    <div>
      <h1>Shopify orders</h1>
      <p>Read-only archive of orders exported before the Shopify store was shut down.</p>
    </div>
  </div>

  <form class="admin-filters" method="get" action="{{ route('admin.shopify-orders.index') }}">
    <input type="search" name="q" value="{{ $q }}" placeholder="Search order #, email, product, payment ref" />
    @if ($customerId)
      <input type="hidden" name="customer" value="{{ $customerId }}" />
    @endif
    <button class="admin-button" type="submit">Apply</button>
    @if ($q !== '' || $customerId)
      <a class="admin-link-button secondary" href="{{ route('admin.shopify-orders.index') }}">Clear</a>
    @endif
  </form>

  @if ($customerId)
    <p class="admin-muted" style="margin-bottom: 1rem;">
      Filtered to Shopify customer #{{ $customerId }}.
      <a href="{{ route('admin.shopify-customers.show', $customerId) }}">Open customer</a>
    </p>
  @endif

  <section class="admin-panel">
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Id</th>
            <th>Email</th>
            <th>Financial Status</th>
            <th>Paid At</th>
            <th>Fulfillment Status</th>
            <th>Sub total</th>
            <th>Taxes</th>
            <th>Total</th>
            <th>Discount code</th>
            <th>Discount amount</th>
            <th>Created at</th>
            <th>Lineitem name</th>
            <th>Lineitem Qty</th>
            <th>Lineitem price</th>
            <th>Lineitem compare at price</th>
            <th>Shipping address</th>
            <th>Billing address</th>
            <th>Note attributes</th>
            <th>Cancelled at</th>
            <th>Payment method</th>
            <th>Payment reference</th>
            <th>Refunded amount</th>
            <th>Tax value</th>
            <th class="admin-table-actions-cell">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($orders as $order)
            <tr>
              <td>{{ $order->order_number ?? $order->shopify_id ?? '—' }}</td>
              <td>
                @if ($order->customer)
                  <a href="{{ route('admin.shopify-customers.show', $order->customer) }}">{{ $order->email }}</a>
                @else
                  {{ $order->email ?? '—' }}
                @endif
              </td>
              <td>{{ $order->financial_status ?? '—' }}</td>
              <td>{{ $order->paid_at?->format('d M Y, h:i A') ?? '—' }}</td>
              <td>{{ $order->fulfillment_status ?? '—' }}</td>
              <td>{{ $order->formattedMoney($order->subtotal) }}</td>
              <td>{{ $order->formattedMoney($order->taxes) }}</td>
              <td>{{ $order->formattedMoney($order->total) }}</td>
              <td>{{ $order->discount_code ?? '—' }}</td>
              <td>{{ $order->formattedMoney($order->discount_amount) }}</td>
              <td>{{ $order->shopify_created_at?->format('d M Y, h:i A') ?? '—' }}</td>
              <td class="admin-product-list-cell">{{ $order->lineitem_name ?? '—' }}</td>
              <td>{{ $order->lineitem_qty ?? '—' }}</td>
              <td>{{ $order->formattedMoney($order->lineitem_price) }}</td>
              <td>{{ $order->formattedMoney($order->lineitem_compare_at_price) }}</td>
              <td class="admin-product-list-cell">{{ $order->shipping_address ?? '—' }}</td>
              <td class="admin-product-list-cell">{{ $order->billing_address ?? '—' }}</td>
              <td class="admin-product-list-cell">{{ \Illuminate\Support\Str::limit($order->note_attributes ?? '—', 80) }}</td>
              <td>{{ $order->cancelled_at?->format('d M Y, h:i A') ?? '—' }}</td>
              <td class="admin-product-list-cell">{{ $order->payment_method ?? '—' }}</td>
              <td>{{ $order->payment_reference ?? '—' }}</td>
              <td>{{ $order->formattedMoney($order->refunded_amount) }}</td>
              <td>{{ $order->formattedMoney($order->tax_value) }}</td>
              <td class="admin-table-actions-cell">
                <a class="admin-link-button secondary" href="{{ route('admin.shopify-orders.show', $order) }}">View</a>
              </td>
            </tr>
          @empty
            <tr class="admin-table-empty">
              <td colspan="24">No Shopify orders found. Run <code>php artisan shopify:import-archive --fresh</code>.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if ($orders->hasPages())
      <div class="admin-pagination">
        {{ $orders->links('vendor.pagination.admin') }}
      </div>
    @endif
  </section>
@endsection
