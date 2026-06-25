@extends('layouts.admin')

@section('title', 'Customers | Oakter Admin')

@section('content')
  <div class="admin-topbar">
    <div>
      <h1>Customers</h1>
      <p>View customers who have checked out on the website.</p>
    </div>
    <a class="admin-link-button secondary" href="{{ $exportUrl }}">Export CSV</a>
  </div>

  <form class="admin-filters" method="get" action="{{ route('admin.customers.index') }}">
    <input
      type="search"
      name="q"
      value="{{ old('q', $filters['q']) }}"
      placeholder="Search name, email, or phone"
    />
    <button class="admin-button" type="submit">Search</button>
    @if ($hasActiveFilters)
      <a class="admin-link-button secondary" href="{{ route('admin.customers.index') }}">Clear</a>
    @endif
  </form>

  <section class="admin-panel">
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Customer</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Orders</th>
            <th>Abandoned</th>
            <th>Total spent</th>
            <th>Joined</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($customers as $customer)
            <tr>
              <td>{{ $customer->name }}</td>
              <td>{{ $customer->email }}</td>
              <td>{{ $customer->phone ?? '—' }}</td>
              <td>{{ number_format($customer->total_orders) }}</td>
              <td>{{ number_format($customer->abandoned_orders_count) }}</td>
              <td>{{ $customer->formattedTotalSpent() }}</td>
              <td>{{ $customer->created_at->format('d M Y') }}</td>
            </tr>
          @empty
            <tr class="admin-table-empty">
              <td colspan="7">No customers found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if ($customers->hasPages())
      <div class="admin-pagination">
        {{ $customers->links('vendor.pagination.admin') }}
      </div>
    @endif
  </section>
@endsection
