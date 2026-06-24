<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Admin | Oakter')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap"
      rel="stylesheet"
    />
    <link rel="icon" type="image/png" href="{{ asset('assets/favicon.png') }}" />
    @vite(['resources/css/app.css', 'resources/js/admin.js'])
  </head>
  <body class="admin-body">
    <div class="admin-shell">
      <aside class="admin-sidebar">
        <div class="admin-sidebar-brand">
          <img src="{{ asset('assets/oakter-logo-220.png') }}" alt="Oakter" />
          <span>Admin panel</span>
        </div>

        @include('admin.partials.sidebar-nav')

        <div class="admin-sidebar-footer">
          <div class="admin-sidebar-user">
            <strong>{{ auth()->user()->name }}</strong>
            <span>{{ auth()->user()->email }}</span>
          </div>
          <form class="admin-logout-form" method="post" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit">Log out</button>
          </form>
        </div>
      </aside>

      <main class="admin-main">
        @if (session('status'))
          <div class="admin-alert">{{ session('status') }}</div>
        @endif

        @yield('content')
      </main>
    </div>

    @stack('admin-customer-templates')
    @include('admin.partials.customer-details-dialog')
  </body>
</html>
