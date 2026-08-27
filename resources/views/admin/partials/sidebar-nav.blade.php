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
  @if (auth()->user()->hasAdminPermission(\App\Support\AdminPermissions::NEWS))
    <a href="{{ route('admin.news-articles.index') }}" @class(['is-active' => request()->routeIs('admin.news-articles.*')])>News articles</a>
  @endif
  @if (auth()->user()->isAdmin())
    <a href="{{ route('admin.sub-admins.index') }}" @class(['is-active' => request()->routeIs('admin.sub-admins.*')])>Sub admins</a>
    <a href="{{ route('admin.settings.edit') }}" @class(['is-active' => request()->routeIs('admin.settings.*')])>Settings</a>
  @endif
  @if (auth()->user()->hasAdminPermission(\App\Support\AdminPermissions::CUSTOMERS))
    <a href="{{ route('admin.shopify-customers.index') }}" @class(['is-active' => request()->routeIs('admin.shopify-customers.*')])>Shopify customers</a>
  @endif
  @if (auth()->user()->hasAdminPermission(\App\Support\AdminPermissions::ORDERS))
    <a href="{{ route('admin.shopify-orders.index') }}" @class(['is-active' => request()->routeIs('admin.shopify-orders.*')])>Shopify orders</a>
  @endif
  <a href="{{ route('admin.profile.edit') }}" @class(['is-active' => request()->routeIs('admin.profile.*')])>Profile</a>
</nav>
