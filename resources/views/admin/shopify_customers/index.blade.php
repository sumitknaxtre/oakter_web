@extends('layouts.admin')

@section('title', 'Shopify Customers | Oakter Admin')

@section('content')
  <div class="admin-topbar">
    <div>
      <h1>Shopify customers</h1>
      <p>Read-only archive of customers exported before the Shopify store was shut down.</p>
    </div>
  </div>

  <form class="admin-filters" method="get" action="{{ route('admin.shopify-customers.index') }}">
    <input type="search" name="q" value="{{ $q }}" placeholder="Search ID, name, email, or phone" />
    <button class="admin-button" type="submit">Apply</button>
    @if ($q !== '')
      <a class="admin-link-button secondary" href="{{ route('admin.shopify-customers.index') }}">Clear</a>
    @endif
  </form>

  <section class="admin-panel">
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Email</th>
            <th>Address</th>
            <th>Phones</th>
            <th>Total spent</th>
            <th>Total orders</th>
            <th class="admin-table-actions-cell">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($customers as $customer)
            <tr>
              <td>{{ $customer->shopify_id }}</td>
              <td>{{ $customer->name ?? '—' }}</td>
              <td>{{ $customer->email ?? '—' }}</td>
              <td class="admin-product-list-cell">{{ $customer->address ?? '—' }}</td>
              <td>{{ $customer->phones ?? '—' }}</td>
              <td>{{ $customer->formattedTotalSpent() }}</td>
              <td>
                @if ($customer->orders_count > 0)
                  <a href="{{ route('admin.shopify-orders.index', ['customer' => $customer->id]) }}">
                    {{ number_format($customer->total_orders) }}
                  </a>
                @else
                  {{ number_format($customer->total_orders) }}
                @endif
              </td>
              <td class="admin-table-actions-cell">
                <a class="admin-link-button secondary" href="{{ route('admin.shopify-customers.show', $customer) }}">View</a>
              </td>
            </tr>
          @empty
            <tr class="admin-table-empty">
              <td colspan="8">No Shopify customers found. Run <code>php artisan shopify:import-archive --fresh</code>.</td>
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
