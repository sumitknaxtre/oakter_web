@extends('layouts.admin')

@section('title', 'Dashboard | Oakter Admin')

@section('content')
  <div class="admin-topbar">
    <div>
      <h1>Dashboard</h1>
      <p>Overview of orders and revenue.</p>
    </div>
    <a class="admin-link-button secondary" href="{{ route('website.home') }}" target="_blank" rel="noopener">View website</a>
  </div>

  <div class="admin-card-grid">
    <article class="admin-stat-card">
      <span>Total orders</span>
      <strong>{{ number_format($stats['total_orders']) }}</strong>
    </article>
    <article class="admin-stat-card">
      <span>Revenue</span>
      <strong>₹{{ number_format($stats['revenue'], 2) }}</strong>
    </article>
  </div>

  <section class="admin-panel">
    <div class="admin-panel-head">
      <h2>Recent orders</h2>
      <a class="admin-link-button secondary" href="{{ route('admin.orders.index') }}">View all</a>
    </div>
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Order</th>
            <th>Customer</th>
            <th>Product</th>
            <th>Amount</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($recentOrders as $order)
            <tr>
              <td><a href="{{ route('admin.orders.show', $order) }}">#{{ $order->id }}</a></td>
              <td>@include('admin.partials.order-customer-list-cell', ['order' => $order])</td>
              <td>{{ $order->product_name }}</td>
              <td>{{ $order->formattedAmount() }}</td>
              <td>{{ $order->created_at->format('d M Y, h:i A') }}</td>
            </tr>
          @empty
            <tr class="admin-table-empty">
              <td colspan="5">No orders yet.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>

  @push('admin-customer-templates')
    @foreach ($recentOrders as $order)
      <template id="customer-details-{{ $order->id }}">
        @include('admin.partials.order-customer-modal-content', ['order' => $order])
      </template>
    @endforeach
  @endpush
@endsection
