@extends('layouts.admin')

@section('title', 'Orders | Oakter Admin')

@section('content')
  <div class="admin-topbar">
    <div>
      <h1>Orders</h1>
      <p>Search, filter by date, and export customer orders.</p>
    </div>
    <div class="admin-topbar-actions">
      <a class="admin-link-button secondary" href="{{ $paymentIdsExportUrl }}">Export Payment IDs</a>
      <a class="admin-link-button secondary" href="{{ $exportUrl }}">Export CSV</a>
    </div>
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
    <label class="admin-filter-date">
      <span>Order type</span>
      <select name="type" aria-label="Filter orders by type">
        <option value="" @selected(old('type', $filters['type']) === '')>All orders</option>
        @foreach (\App\Support\AdminOrderFilters::typeOptions() as $typeOption)
          <option value="{{ $typeOption }}" @selected(old('type', $filters['type']) === $typeOption)>
            {{ \App\Support\AdminOrderFilters::typeLabel($typeOption) }}
          </option>
        @endforeach
      </select>
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
            {{-- <th>Fulfillment</th> --}}
            <th>Payment ID</th>
            <th>Date</th>
            <th class="admin-table-actions-cell">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($orders as $order)
            <tr>
              <td>{{ $order->displayOrderCode() }}</td>
              <td>@include('admin.partials.order-customer-list-cell', ['order' => $order])</td>
              <td>{{ $order->product_name }}</td>
              <td>{{ $order->formattedAmount() }}</td>
              <td>{{ $order->coupon_code ?? '—' }}</td>
              <td>
                <div @class(['admin-payment-status-refunded' => $order->isRefunded()])>{{ $order->paymentStatusLabel() }}</div>
                <div class="admin-muted">{{ $order->payment_method ?? '—' }}</div>
              </td>
              {{-- <td>{{ $order->fulfillmentStatusLabel() }}</td> --}}
              <td>{{ $order->razorpay_payment_id ?? '—' }}</td>
              <td>{{ $order->created_at->format('d M Y, h:i A') }}</td>
              <td class="admin-table-actions-cell">
                <div class="admin-table-actions">
                  <a
                    class="admin-link-button secondary"
                    href="{{ route('admin.orders.show', $order) }}"
                    aria-label="View order {{ $order->displayOrderCode() }} details"
                  >
                    View
                  </a>
                </div>
              </td>
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
        {{ $orders->links('vendor.pagination.admin') }}
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
