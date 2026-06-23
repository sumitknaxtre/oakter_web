<header class="site-header">
  <a class="brand" href="{{ route('website.home') }}" aria-label="Oakter home">
    <img src="{{ asset('assets/oakter-logo-280.png') }}" alt="Oakter" />
  </a>
  <button class="menu-toggle" type="button" aria-label="Open menu" aria-expanded="false">
    <span></span><span></span><span></span>
  </button>
  <nav class="primary-nav" aria-label="Primary navigation">
    <a href="{{ route('website.home') }}" @if (request()->routeIs('website.home', 'website.buy_studio_ac')) aria-current="page" @endif>Studio AC</a>
    <a href="{{ route('website.mini_ups') }}" @if (request()->routeIs('website.mini_ups', 'website.buy_mini_ups', 'website.buy_mini_ups_airfiber')) aria-current="page" @endif>Mini UPS</a>
    <a href="{{ route('website.gan_charger') }}" @if (request()->routeIs('website.gan_charger', 'website.buy_gan_charger')) aria-current="page" @endif>GaN Charger</a>
    <a href="{{ route('website.b2b') }}" @if (request()->routeIs('website.b2b')) aria-current="page" @endif>B2B Products</a>
  </nav>
</header>
