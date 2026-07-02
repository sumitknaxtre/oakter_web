@extends('layouts.admin')

@section('title', 'Products | Oakter Admin')

@section('content')
  <div class="admin-topbar">
    <div>
      <h1>Products</h1>
      <p>Catalog products available for checkout.</p>
    </div>
  </div>

  <section class="admin-panel">
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Product</th>
            <th>Slug</th>
            <th>Stock</th>
            <th>Checkout price</th>
            <th>Listing</th>
            <th>Orders</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($products as $product)
            <tr>
              <td>
                <div class="admin-product-cell">
                  @if ($product->thumbPath())
                    <img src="{{ asset($product->thumbPath()) }}" alt="" />
                  @endif
                  <span>{{ $product->name }}</span>
                </div>
              </td>
              <td>{{ $product->slug }}</td>
              <td>
                @if ($product->hide_buy_button)
                  <span class="admin-stock-badge hidden-buy">Buy hidden</span>
                @elseif ($product->is_in_stock)
                  <span class="admin-stock-badge in-stock">In stock</span>
                @else
                  <span class="admin-stock-badge out-of-stock">Out of stock</span>
                @endif
              </td>
              <td>{{ $product->formattedAmount() }}</td>
              <td>
                @if ($product->listingPrice())
                  {{ $product->listingPrice() }}
                  @if ($product->listingMrp())
                    <span class="admin-muted">MRP {{ $product->listingMrp() }}</span>
                  @endif
                @else
                  —
                @endif
              </td>
              <td>{{ number_format($product->orders_count) }}</td>
              <td>
                <a class="admin-link-button secondary" href="{{ route('admin.products.edit', $product) }}">Edit</a>
              </td>
            </tr>
          @empty
            <tr class="admin-table-empty">
              <td colspan="7">No products found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>
@endsection
