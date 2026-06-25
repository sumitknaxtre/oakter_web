@extends('layouts.admin')

@section('title', 'Abandoned orders | Oakter Admin')

@section('content')
  <div class="admin-topbar">
    <div>
      <h1>Abandoned orders</h1>
      <p>Checkouts where payment was started but not completed successfully.</p>
    </div>
  </div>

  <form class="admin-filters" method="get" action="{{ route('admin.abandoned-orders.index') }}">
    <input
      type="search"
      name="q"
      value="{{ old('q', $filters['q']) }}"
      placeholder="Search email, phone, product, Razorpay order ID"
    />
    <button class="admin-button" type="submit">Search</button>
    @if ($hasActiveFilters)
      <a class="admin-link-button secondary" href="{{ route('admin.abandoned-orders.index') }}">Clear</a>
    @endif
  </form>

  <section class="admin-panel">
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Attempt</th>
            <th>Customer</th>
            <th>Product</th>
            <th>Amount</th>
            <th>Payment</th>
            <th>Razorpay order</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($orders as $order)
            <tr>
              <td>#{{ $order->id }}</td>
              <td>
                @include('admin.partials.customer-list-cell', [
                  'record' => $order,
                  'dialogId' => 'customer-details-abandoned-'.$order->id,
                  'dialogLabel' => 'Abandoned #'.$order->id,
                ])
              </td>
              <td>{{ $order->product_name }}</td>
              <td>{{ $order->formattedAmount() }}</td>
              <td>{{ $order->paymentStatusLabel() }}</td>
              <td>{{ $order->razorpay_order_id }}</td>
              <td>{{ $order->created_at->format('d M Y, h:i A') }}</td>
            </tr>
          @empty
            <tr class="admin-table-empty">
              <td colspan="7">No abandoned orders found.</td>
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
      <template id="customer-details-abandoned-{{ $order->id }}">
        @include('admin.partials.customer-modal-content', ['record' => $order])
      </template>
    @endforeach
  @endpush
@endsection
