@extends('layouts.admin')

@section('title', ($customer->name ?? $customer->email ?? 'Shopify customer').' | Oakter Admin')

@section('content')
  <div class="admin-topbar">
    <div>
      <h1>{{ $customer->name ?? 'Shopify customer' }}</h1>
      <p>Shopify archive customer #{{ $customer->shopify_id }}</p>
    </div>
    <a class="admin-link-button secondary" href="{{ route('admin.shopify-customers.index') }}">Back to Shopify customers</a>
  </div>

  <section class="admin-panel">
    <div class="admin-panel-head">
      <h2>Customer details</h2>
    </div>
    <div class="admin-detail-grid">
      <dl class="admin-detail-list">
        <div><dt>Id</dt><dd>{{ $customer->shopify_id }}</dd></div>
        <div><dt>Name</dt><dd>{{ $customer->name ?? '—' }}</dd></div>
        <div><dt>Email</dt><dd>{{ $customer->email ?? '—' }}</dd></div>
        <div><dt>Phones</dt><dd>{{ $customer->phones ?? '—' }}</dd></div>
        <div><dt>Address</dt><dd>{{ $customer->address ?? '—' }}</dd></div>
        <div><dt>Total spent</dt><dd>{{ $customer->formattedTotalSpent() }}</dd></div>
        <div><dt>Total orders (CSV)</dt><dd>{{ number_format($customer->total_orders) }}</dd></div>
      </dl>
    </div>
  </section>

  <section class="admin-panel" style="margin-top: 1.5rem;">
    <div class="admin-panel-head">
      <h2>Linked Shopify orders</h2>
      @if ($customer->orders->isNotEmpty())
        <a class="admin-link-button secondary" href="{{ route('admin.shopify-orders.index', ['customer' => $customer->id]) }}">View all</a>
      @endif
    </div>

    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Order</th>
            <th>Financial</th>
            <th>Fulfillment</th>
            <th>Total</th>
            <th>Lineitem</th>
            <th>Created at</th>
            <th class="admin-table-actions-cell">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($customer->orders as $order)
            <tr>
              <td>{{ $order->order_number ?? $order->shopify_id ?? '—' }}</td>
              <td>{{ $order->financial_status ?? '—' }}</td>
              <td>{{ $order->fulfillment_status ?? '—' }}</td>
              <td>{{ $order->formattedMoney($order->total) }}</td>
              <td class="admin-product-list-cell">{{ $order->lineitem_name ?? '—' }}</td>
              <td>{{ $order->shopify_created_at?->format('d M Y, h:i A') ?? '—' }}</td>
              <td class="admin-table-actions-cell">
                <a class="admin-link-button secondary" href="{{ route('admin.shopify-orders.show', $order) }}">View</a>
              </td>
            </tr>
          @empty
            <tr class="admin-table-empty">
              <td colspan="7">No linked Shopify orders for this email.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>
@endsection
