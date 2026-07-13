<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Checkout | Oakter')</title>
    <meta name="description" content="@yield('meta_description', 'Secure checkout for Oakter products.')" />
    <meta name="robots" content="noindex, nofollow" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap"
      rel="stylesheet"
    />
    <link rel="icon" type="image/png" href="{{ asset('assets/favicon.png') }}" />
    @include('website.partials.meta-pixel')
    @include('website.partials.google-analytics')
    @vite(['resources/css/app.css', 'resources/js/checkout.js'])
    @stack('head')
  </head>
  <body class="page-checkout">
    <header class="checkout-header">
      <a class="checkout-brand" href="{{ route('website.home') }}" aria-label="Oakter home">
        <img src="{{ asset('assets/oakter-logo-280.png') }}" alt="Oakter" />
      </a>
    </header>

    @yield('content')

    @stack('scripts')
    @stack('meta_pixel_events')
    @stack('ga_events')
  </body>
</html>
