@extends('layouts.admin')

@section('title', 'Dealers | Oakter Admin')

@section('content')
  <div class="admin-topbar">
    <div>
      <h1>Dealers</h1>
      <p>Manage Studio AC retail outlets shown on the Find a store page.</p>
    </div>
    <a class="admin-link-button" href="{{ route('admin.dealers.create') }}">Add dealer</a>
  </div>

  <form class="admin-filters" method="get" action="{{ route('admin.dealers.index') }}">
    <input type="search" name="q" value="{{ $q }}" placeholder="Search name, state, district, or phone" />
    <button class="admin-button" type="submit">Apply</button>
    @if ($q !== '')
      <a class="admin-link-button secondary" href="{{ route('admin.dealers.index') }}">Clear</a>
    @endif
  </form>

  <section class="admin-panel">
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>State</th>
            <th>District</th>
            <th>Address</th>
            <th>Phone</th>
            <th>Status</th>
            {{-- <th>Sort</th> --}}
            <th class="admin-table-actions-cell">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($dealers as $dealer)
            <tr>
              <td class="admin-product-list-cell"><strong>{{ $dealer->name }}</strong></td>
              <td>{{ $dealer->state }}</td>
              <td>{{ $dealer->district }}</td>
              <td class="admin-dealer-address-cell">
                <span class="admin-dealer-address" title="{{ $dealer->address }}">{{ $dealer->address }}</span>
              </td>
              <td>{{ $dealer->phone ?? '—' }}</td>
              <td>
                @if ($dealer->is_active)
                  <span class="admin-badge is-paid">Active</span>
                @else
                  <span class="admin-badge is-pending">Inactive</span>
                @endif
              </td>
              {{-- <td>{{ $dealer->sort_order }}</td> --}}
              <td class="admin-table-actions-cell">
                <div class="admin-table-actions">
                  <a class="admin-link-button secondary" href="{{ route('admin.dealers.edit', $dealer) }}">Edit</a>
                  <form method="post" action="{{ route('admin.dealers.destroy', $dealer) }}" onsubmit="return confirm('Delete this dealer?');">
                    @csrf
                    @method('DELETE')
                    <button class="admin-link-button secondary" type="submit">Delete</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr class="admin-table-empty">
              <td colspan="7">No dealers found. Run <code>php artisan db:seed --class=DealerSeeder</code>.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if ($dealers->hasPages())
      <div class="admin-pagination">
        {{ $dealers->links('vendor.pagination.admin') }}
      </div>
    @endif
  </section>
@endsection
