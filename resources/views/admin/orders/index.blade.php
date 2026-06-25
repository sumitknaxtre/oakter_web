@extends('layouts.admin')

@section('title', 'Orders | Oakter Admin')

@section('content')
  <div class="admin-topbar">
    <div>
      <h1>Orders</h1>
      <p>Search, filter by date, and export customer orders.</p>
    </div>
    <a class="admin-link-button secondary" href="{{ $exportUrl }}">Export CSV</a>
  </div>

  <form class="admin-filters" method="get" action="{{ route('admin.orders.index') }}">
    <input type="search" name="q" value="{{ old('q', $filters['q']) }}" placeholder="Search email, phone, product, payment ID" />
    <label class="admin-filter-date" style="margin-left: 2rem;">
      <span>From</span>
      <input
        type="date"
        name="from"
        value="{{ old('from', $filters['from']) }}"
        max="{{ old('to', $filters['to']) !== '' ? min(old('to', $filters['to']), $maxFilterDate) : $maxFilterDate }}"
      />
    </label>
    <label class="admin-filter-date">
      <span>To</span>
      <input
        type="date"
        name="to"
        value="{{ old('to', $filters['to']) }}"
        min="{{ old('from', $filters['from']) }}"
        max="{{ $maxFilterDate }}"
      />
    </label>
    <button class="admin-button" type="submit">Apply</button>
    @if ($hasActiveFilters)
      <a class="admin-link-button secondary" href="{{ route('admin.orders.index') }}">Clear</a>
    @endif
    @error('from')<p class="admin-filter-error">{{ $message }}</p>@enderror
    @error('to')<p class="admin-filter-error">{{ $message }}</p>@enderror
  </form>

  <section class="admin-panel">
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Order</th>
            <th>Customer</th>
            <th>Product</th>
            <th>Amount</th>
            <th>Coupon</th>
            <th>Payment</th>
            <th>Fulfillment</th>
            <th>Payment ID</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($orders as $order)
            <tr>
              <td><a href="{{ route('admin.orders.show', $order) }}">#{{ $order->id }}</a></td>
              <td>@include('admin.partials.order-customer-list-cell', ['order' => $order])</td>
              <td>{{ $order->product_name }}</td>
              <td>{{ $order->formattedAmount() }}</td>
              <td>{{ $order->coupon_code ?? '—' }}</td>
              <td>
                <div>{{ $order->paymentStatusLabel() }}</div>
                <div class="admin-muted">{{ $order->payment_method ?? '—' }}</div>
              </td>
              <td>{{ $order->fulfillmentStatusLabel() }}</td>
              <td>{{ $order->razorpay_payment_id ?? '—' }}</td>
              <td>{{ $order->created_at->format('d M Y, h:i A') }}</td>
            </tr>
          @empty
            <tr class="admin-table-empty">
              <td colspan="9">No orders found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if ($orders->hasPages())
      <div class="admin-pagination">
        {{ $orders->links() }}
      </div>
    @endif
  </section>

  @push('admin-customer-templates')
    @foreach ($orders as $order)
      <template id="customer-details-{{ $order->id }}">
        @include('admin.partials.order-customer-modal-content', ['order' => $order])
      </template>
    @endforeach
  @endpush
@endsection
