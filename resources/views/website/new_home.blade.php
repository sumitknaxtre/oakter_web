@extends('layouts.website')

@php($studioAc = \App\Support\ProductCatalog::forConfigKey('studio_ac'))

@section('title', 'Oakter Studio AC 5000 | 0.5 Ton Window AC for Small Rooms')
@section('meta_description', 'Buy Oakter Studio AC 5000, a compact 0.5 Ton 3 Star inverter window AC for bedrooms, studies, cabins, shops and small offices.')
@section('canonical', url('/new-home'))
@section('robots', 'noindex,nofollow')
@section('og_type', 'product')
@section('og_image', asset('assets/oakter-logo-1200.png'))

@section('structured_data')
    <script type="application/ld+json">{"@@context":"https://schema.org","@@type":"Organization","name":"Oakter","url":"https://www.oakter.com","logo":"{{ asset('assets/oakter-logo-1200.png') }}","sameAs":["https://www.instagram.com/oyeoakter/","https://www.facebook.com/oakter/","https://www.youtube.com/channel/UC3h_V9-78yWVbtTi5eNWvZQ"],"contactPoint":[{"@@type":"ContactPoint","telephone":"+91-75750-40506","contactType":"customer support","areaServed":"IN"}]}</script>
    <script type="application/ld+json">{"@@context":"https://schema.org","@@type":"Product","name":"Oakter Studio AC 5000","brand":{"@@type":"Brand","name":"Oakter"},"description":"Compact 0.5 Ton window AC for small rooms, bedrooms, studies, cabins, shops and small offices.","image":"{{ asset('assets/oakter-logo-1200.png') }}","offers":{"@@type":"Offer","priceCurrency":"INR","price":"16999","availability":"https://schema.org/InStock","url":"{{ url('/new-home') }}"}}</script>
    <script type="application/ld+json">{"@@context":"https://schema.org","@@type":"BreadcrumbList","itemListElement":[{"@@type":"ListItem","position":1,"name":"Home","item":"{{ url('/new-home') }}"}]}</script>
@endsection

@section('main_id', 'top')

@section('content')
      <section class="hero" id="studio-ac">
        <div class="hero-copy">
          <img class="studio-logo-mark" src="{{ asset('assets/studioac5000logo-tight-9vHKEXiT.png') }}" alt="Studio AC 5000" />
          <h1>Effective &amp; efficient <br class="h1-break" />room cooling!</h1>
          <span class="hero-accent-line" aria-hidden="true"></span>
          <p class="hero-lede">
            Oakter Studio AC is a compact 0.5 Ton window AC for bedrooms, studies,
            cabins, shops &amp; small offices.
          </p>
        </div>
        <div class="hero-product">
          <div class="hero-media" aria-label="Studio AC product image">
            <img
              src="{{ asset('assets/studio-ac-render-BTwPeX3n.png') }}"
              alt="Oakter Studio AC 5000"
            />
            <div class="floating-spec top">
              <span>0.5 Ton</span>
              <strong>Upto 120 Sq ft</strong>
            </div>
            <div class="floating-spec bottom">
              <span>3 Star</span>
              <strong>Electricity cost ₹4/hr</strong>
            </div>
          </div>
          <div class="desktop-spec-row" aria-label="Studio AC key highlights">
            <div class="floating-spec">
              <span>0.5 Ton</span>
              <strong>Upto 120 Sq ft</strong>
            </div>
            <div class="floating-spec">
              <span>3 Star</span>
              <strong>Electricity cost ₹4/hr</strong>
            </div>
          </div>
          <div class="buy-block" aria-label="Studio AC purchase options">
            <div class="price-row">
              <strong>{{ $studioAc['listing']['price'] }}</strong>
              <span>MRP {{ $studioAc['listing']['mrp'] }}</span>
            </div>
            <div class="cta-row">
              @include('website.partials.product-buy-button', [
                'inStock' => $studioAc['is_in_stock'] ?? true,
                'hideBuyButton' => $studioAc['hide_buy_button'] ?? false,
                'href' => route('website.retail_outlets'),
                'label' => 'Find A Retail Outlet',
              ])
            </div>
            <div class="marketplaces compact hero-marketplaces">
              <strong>Also available on</strong>
              <a href="https://www.flipkart.com/oakter-2026-model-0-5-ton-3-star-window-inverter-ac/p/itm3bc683b8e4445?pid=ACNHMYES7AHCAKTF" aria-label="Buy on Flipkart">
                <img src="{{ asset('assets/mp-flipkart-B4UvsN8l.webp') }}" alt="Flipkart" />
              </a>
              <a href="https://www.amazon.in/dp/B0GZVLQ8VT" aria-label="Buy on Amazon">
                <img src="{{ asset('assets/mp-amazon-DztdINrT.png') }}" alt="Amazon" />
              </a>
              <a href="https://blinkit.com/prn/oakter-0.5-ton-3-star-inverter-studio-window-ac/prid/788007" aria-label="Buy on Blinkit">
                <img src="{{ asset('assets/mp-blinkit-CY2t1LLC.png') }}" alt="Blinkit" />
              </a>
            </div>
          </div>
        </div>
      </section>

      <section class="section intro" id="compare">
        <div>
          <p class="eyebrow">Studio AC</p>
          <h2>Designed from first principles for Indian homes.</h2>
        </div>
        <p>
          Studio AC delivers lower running cost, simpler installation, smaller footprint and optimal
          cooling for the rooms we actually use every day.
        </p>
      </section>

      <div class="carousel-shell feature-carousel-shell">
        <section class="feature-grid" data-carousel>
          <article>
            <span>01</span>
            <h3>Built for Indian conditions</h3>
            <p>Designed for tropical, high humidity weather conditions.</p>
          </article>
          <article>
            <span>02</span>
            <h3>Lowest electricity cost</h3>
            <p>Consumes only 525W, that's a running cost of ₹4/hour for residential usage.</p>
          </article>
          <article>
            <span>03</span>
            <h3>Ease of installation</h3>
            <div class="feature-points">
              <p>Cut an opening of about 1.6ft X 1.4ft and mount with 6 screws</p>
            </div>
          </article>
        </section>
        <div class="carousel-dots" aria-label="Feature carousel controls"></div>
      </div>

      <section class="pdp-section ac-support-section" aria-label="Studio AC support and specifications">
        <div class="pdp-panel">
          <div class="carousel-shell service-carousel-shell">
            <div class="service-icons" data-carousel>
              <div>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2v20M3.34 7l17.32 10M3.34 17 20.66 7M12 2l-2 2M12 2l2 2M12 22l-2-2M12 22l2-2M3.34 7l2.73.73M3.34 7l.73 2.73M20.66 17l-2.73-.73M20.66 17l-.73-2.73M3.34 17l.73-2.73M3.34 17l2.73-.73M20.66 7l-.73 2.73M20.66 7l-2.73.73"/></svg>
                <strong>0.5 Ton 3 Star Inverter AC</strong>
              </div>
              <div>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 1.5c.88 0 1.68.47 2.12 1.23l7.04 12.2a2.45 2.45 0 0 1-2.12 3.68H4.96a2.45 2.45 0 0 1-2.12-3.68l7.04-12.2A2.45 2.45 0 0 1 12 1.5z"/><circle cx="12" cy="7.9" r="1.05"/><circle cx="8" cy="15.1" r="1.05"/><circle cx="16" cy="15.1" r="1.05"/></svg>
                <strong>Small 3pin 6Amp Plug</strong>
              </div>
              <div>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l7 3v5c0 5-3 8-7 10-4-2-7-5-7-10V6z"/><path d="M9 12l2 2 4-5"/></svg>
                <strong>1-year product warranty</strong>
              </div>
              <div>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l7 3v5c0 5-3 8-7 10-4-2-7-5-7-10V6z"/><path d="M9 12l2 2 4-5"/></svg>
                <strong>5-year compressor warranty</strong>
              </div>
            </div>
            <div class="carousel-dots" aria-label="Service carousel controls"></div>
          </div>
          <dl class="spec-list studio-spec-list">
            <div class="spec-list-title"><dt class="room-size-heading">RECOMMENDED ROOM SIZE</dt></div>
            <div>
              <dt>
                <strong>Rooms with medium heat-load</strong>
                <span>Bedrooms, study, home office, shops...</span>
              </dt>
            </div>
            <div class="spec-list-value"><dd><span>Upto 120</span><span>sq ft</span></dd></div>
            <div>
              <dt>
                <strong>Rooms with high heat-load</strong>
                <span>With all-day sun facing wall / Top Floor / Leaky doors or windows</span>
              </dt>
            </div>
            <div class="spec-list-value"><dd><span>Upto 75</span><span>sq ft</span></dd></div>
          </dl>
        </div>
      </section>
@endsection
