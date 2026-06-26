<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Oakter')</title>
    <meta name="description" content="@yield('meta_description', '')" />
    @hasSection('robots')
    <meta name="robots" content="@yield('robots')" />
    @endif
    <link rel="canonical" href="@yield('canonical', url()->current())" />
    <meta property="og:site_name" content="Oakter" />
    <meta property="og:type" content="@yield('og_type', 'website')" />
    <meta property="og:title" content="@yield('og_title', trim($__env->yieldContent('title')))" />
    <meta property="og:description" content="@yield('og_description', trim($__env->yieldContent('meta_description')))" />
    <meta property="og:url" content="@yield('canonical', url()->current())" />
    <meta property="og:image" content="@yield('og_image', asset('assets/oakter-logo-1200.png'))" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="@yield('og_title', trim($__env->yieldContent('title')))" />
    <meta name="twitter:description" content="@yield('og_description', trim($__env->yieldContent('meta_description')))" />
    <meta name="twitter:image" content="@yield('og_image', asset('assets/oakter-logo-1200.png'))" />
    @yield('structured_data')
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap"
      rel="stylesheet"
    />
    <link rel="icon" type="image/png" href="{{ asset('assets/favicon.png') }}" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body @class([
    'page-studio' => request()->routeIs(
        'website.home',
        'website.buy_studio_ac',
        'website.legacy.collections.mini_ac',
        'website.legacy.products.studio_ac',
        'website.legacy.blogs.news',
        'website.legacy.pages.warranty_policy',
    ),
    'page-buy' => request()->routeIs([
      'website.buy_studio_ac',
      'website.buy_mini_ups',
      'website.buy_mini_ups_airfiber',
      'website.buy_gan_charger',
    ]),
    'page-ups' => request()->routeIs(
        'website.mini_ups',
        'website.buy_mini_ups',
        'website.buy_mini_ups_airfiber',
        'website.legacy.products.mini_ups_pro',
        'website.legacy.products.mini_ups_12v',
        'website.legacy.products.mini_ups_airfiber',
        'website.legacy.mini_ups_airfiber',
    ),
    'page-gan' => request()->routeIs('website.gan_charger', 'website.buy_gan_charger'),
    'page-b2b' => request()->routeIs('website.collections.all'),
    'page-about' => request()->routeIs('website.about', 'website.legacy.pages.about_us', 'website.media_insights'),
    'page-contact' => request()->routeIs('website.contact', 'website.legacy.pages.contact_us', 'website.legacy.pages.support'),
    'page-privacy' => request()->routeIs('website.privacy'),
  ])>
    @include('website.partials.header')

    @hasSection('main_id')
    <main id="@yield('main_id')">
    @else
    <main>
    @endif
      @yield('content')
    </main>

    @include('website.partials.footer')
  </body>
</html>
