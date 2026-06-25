<nav class="admin-nav" aria-label="Admin navigation">
  @if (auth()->user()->hasAdminPermission(\App\Support\AdminPermissions::DASHBOARD))
    <a href="{{ route('admin.dashboard') }}" @class(['is-active' => request()->routeIs('admin.dashboard')])>Dashboard</a>
  @endif
  @if (auth()->user()->hasAdminPermission(\App\Support\AdminPermissions::ORDERS))
    <a href="{{ route('admin.orders.index') }}" @class(['is-active' => request()->routeIs('admin.orders.*') && ! request()->routeIs('admin.abandoned-orders.*')])>Orders</a>
    <a href="{{ route('admin.abandoned-orders.index') }}" @class(['is-active' => request()->routeIs('admin.abandoned-orders.*')])>Abandoned orders</a>
  @endif
  @if (auth()->user()->hasAdminPermission(\App\Support\AdminPermissions::CUSTOMERS))
    <a href="{{ route('admin.customers.index') }}" @class(['is-active' => request()->routeIs('admin.customers.*')])>Customers</a>
  @endif
  @if (auth()->user()->hasAdminPermission(\App\Support\AdminPermissions::PRODUCTS))
    <a href="{{ route('admin.products.index') }}" @class(['is-active' => request()->routeIs('admin.products.*')])>Products</a>
  @endif
  @if (auth()->user()->hasAdminPermission(\App\Support\AdminPermissions::COUPONS))
    <a href="{{ route('admin.coupons.index') }}" @class(['is-active' => request()->routeIs('admin.coupons.*')])>Coupons</a>
  @endif
  @if (auth()->user()->isAdmin())
    <a href="{{ route('admin.sub-admins.index') }}" @class(['is-active' => request()->routeIs('admin.sub-admins.*')])>Sub admins</a>
  @endif
  <a href="{{ route('admin.profile.edit') }}" @class(['is-active' => request()->routeIs('admin.profile.edit', 'admin.profile.update')])>Profile</a>
  <a href="{{ route('admin.profile.password.edit') }}" @class(['is-active' => request()->routeIs('admin.profile.password.*')])>Change password</a>
</nav>
