@extends('layouts.admin')

@section('title', 'Coupons | Oakter Admin')

@section('content')
  <div class="admin-topbar">
    <div>
      <h1>Coupons</h1>
      <p>Create and manage flat discount codes for selected products.</p>
    </div>
    <a class="admin-link-button" href="{{ route('admin.coupons.create') }}">Add coupon</a>
  </div>

  <section class="admin-panel">
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Code</th>
            <th>Discount</th>
            <th>Products</th>
            <th>Status</th>
            <th>Usage</th>
            <th>Schedule</th>
            <th class="admin-table-actions-cell">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($coupons as $coupon)
            <tr>
              <td><strong>{{ $coupon->code }}</strong></td>
              <td>{{ $coupon->formattedDiscount() }}</td>
              <td>{{ $coupon->products->pluck('name')->join(', ') }}</td>
              <td>
                @if ($coupon->is_active)
                  <span class="admin-badge is-paid">Active</span>
                @else
                  <span class="admin-badge is-failed">Inactive</span>
                @endif
              </td>
              <td>{{ number_format($coupon->used_count) }}@if ($coupon->usage_limit) / {{ number_format($coupon->usage_limit) }}@endif</td>
              <td>
                @if ($coupon->starts_at || $coupon->ends_at)
                  {{ $coupon->starts_at?->format('d M Y') ?? '—' }}
                  –
                  {{ $coupon->ends_at?->format('d M Y') ?? '—' }}
                @else
                  Always on
                @endif
              </td>
              <td class="admin-table-actions-cell">
                <div class="admin-table-actions">
                  <a class="admin-link-button secondary" href="{{ route('admin.coupons.edit', $coupon) }}">Edit</a>
                  <form method="post" action="{{ route('admin.coupons.destroy', $coupon) }}" onsubmit="return confirm('Delete this coupon?');">
                    @csrf
                    @method('DELETE')
                    <button class="admin-link-button secondary" type="submit">Delete</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr class="admin-table-empty">
              <td colspan="7">No coupons found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>
@endsection
