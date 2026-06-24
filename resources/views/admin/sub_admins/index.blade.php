@extends('layouts.admin')

@section('title', 'Sub admins | Oakter Admin')

@section('content')
  <div class="admin-topbar">
    <div>
      <h1>Sub admins</h1>
      <p>Create sub admin accounts and control sidebar access.</p>
    </div>
    <a class="admin-link-button" href="{{ route('admin.sub-admins.create') }}">Add sub admin</a>
  </div>

  <section class="admin-panel">
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Permissions</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($subAdmins as $subAdmin)
            @php
              $permissionLabels = collect($subAdmin->admin_permissions ?? [])
                ->map(fn (string $key) => \App\Support\AdminPermissions::sidebarOptions()[$key] ?? $key)
                ->all();
            @endphp
            <tr>
              <td>{{ $subAdmin->name }}</td>
              <td>{{ $subAdmin->email }}</td>
              <td>
                @if ($permissionLabels !== [])
                  {{ implode(', ', $permissionLabels) }}
                @else
                  <span class="admin-muted">No sidebar access</span>
                @endif
              </td>
              <td>
                <div class="admin-table-actions">
                  <a class="admin-link-button secondary" href="{{ route('admin.sub-admins.edit', $subAdmin) }}">Edit</a>
                  <form method="post" action="{{ route('admin.sub-admins.destroy', $subAdmin) }}" onsubmit="return confirm('Delete this sub admin?');">
                    @csrf
                    @method('DELETE')
                    <button class="admin-link-button secondary" type="submit">Delete</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr class="admin-table-empty">
              <td colspan="4">No sub admins yet.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>
@endsection
