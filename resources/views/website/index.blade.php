@extends('layouts.website')

@php($studioAc = \App\Support\ProductCatalog::forConfigKey('studio_ac'))

@section('title', 'Oakter Studio AC 5000 | 0.5 Ton Window AC for Small Rooms')
@section('meta_description', 'Buy Oakter Studio AC 5000, a compact 0.5 Ton 3 Star inverter window AC for bedrooms, studies, cabins, shops and small offices.')
@section('canonical', url('/'))
@section('og_type', 'product')
@section('og_image', asset('assets/oakter-logo-1200.png'))

@section('structured_data')
    <script type="application/ld+json">{"@@context":"https://schema.org","@@type":"Organization","name":"Oakter","url":"https://www.oakter.com","logo":"{{ asset('assets/oakter-logo-1200.png') }}","sameAs":["https://www.instagram.com/oyeoakter/","https://www.facebook.com/oakter/","https://www.youtube.com/channel/UC3h_V9-78yWVbtTi5eNWvZQ"],"contactPoint":[{"@@type":"ContactPoint","telephone":"+91-75750-40506","contactType":"customer support","areaServed":"IN"}]}</script>
    <script type="application/ld+json">{"@@context":"https://schema.org","@@type":"Product","name":"Oakter Studio AC 5000","brand":{"@@type":"Brand","name":"Oakter"},"description":"Compact 0.5 Ton window AC for small rooms, bedrooms, studies, cabins, shops and small offices.","image":"{{ asset('assets/oakter-logo-1200.png') }}","offers":{"@@type":"Offer","priceCurrency":"INR","price":"16999","availability":"https://schema.org/InStock","url":"https://www.oakter.com/"}}</script>
    <script type="application/ld+json">{"@@context":"https://schema.org","@@type":"BreadcrumbList","itemListElement":[{"@@type":"ListItem","position":1,"name":"Home","item":"https://www.oakter.com/"}]}</script>
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
              <strong>For 75 sq. ft.</strong>
            </div>
            <div class="floating-spec bottom">
              <span>3 Star</span>
              <strong>Electricity cost ₹5/hr</strong>
            </div>
          </div>
          <div class="desktop-spec-row" aria-label="Studio AC key highlights">
            <div class="floating-spec">
              <span>0.5 Ton</span>
              <strong>For 75 sq. ft.</strong>
            </div>
            <div class="floating-spec">
              <span>3 Star</span>
              <strong>Electricity cost ₹5/hr</strong>
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
                'href' => route('website.buy_studio_ac'),
                'label' => 'Buy Studio AC',
              ])
            </div>
            <div class="marketplaces compact hero-marketplaces">
              <strong>Available on</strong>
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
          <p class="eyebrow">Why Studio AC leads</p>
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
            <h3>No rewiring drama</h3>
            <p>Uses a standard 6 Amp plug, so the purchase decision does not get stuck on electrical work.</p>
          </article>
          <article>
            <span>02</span>
            <h3>Built for Indian conditions</h3>
            <p>Designed for tropical, high humidity weather conditions.</p>
          </article>
          <article>
            <span>03</span>
            <h3>Lowest electricity cost</h3>
            <p>Consumes only 500W, that's a running cost of ₹5/hr for residential usage.</p>
          </article>
        </section>
        <div class="carousel-dots" aria-label="Feature carousel controls"></div>
      </div>

      <section class="pdp-section ac-support-section" aria-label="Studio AC support and specifications">
        <div class="pdp-panel">
          <div class="carousel-shell service-carousel-shell">
            <div class="service-icons" data-carousel>
              <div>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l7 3v5c0 5-3 8-7 10-4-2-7-5-7-10V6z"/><path d="M9 12l2 2 4-5"/></svg>
                <strong>1-year product warranty</strong>
              </div>
              <div>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l7 3v5c0 5-3 8-7 10-4-2-7-5-7-10V6z"/><path d="M9 12l2 2 4-5"/></svg>
                <strong>5-year compressor warranty</strong>
              </div>
              <div>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7h11v10H3z"/><path d="M14 11h4l3 3v3h-7z"/><path d="M7 20a2 2 0 1 0 0-4 2 2 0 0 0 0 4zM18 20a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/></svg>
                <strong>Free shipping</strong>
              </div>
              <div>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 12a8 8 0 0 1 13-6"/><path d="M17 3v5h-5"/><path d="M20 12a8 8 0 0 1-13 6"/><path d="M7 21v-5h5"/></svg>
                <strong>7-day replacement</strong>
              </div>
            </div>
            <div class="carousel-dots" aria-label="Service carousel controls"></div>
          </div>
          <dl class="spec-list">
            <div><dt>Capacity</dt><dd>0.5 Ton</dd></div>
            <div>
              <dt>Energy rating</dt>
              <dd>
                <span class="bee-rating" aria-label="3 out of 5 star energy rating">
                  <svg viewBox="0 0 180 92" role="img" aria-hidden="true">
                    <path class="bee-fill" d="M9 84A81 81 0 0 1 119 6L91 84H9Z" />
                    <path class="bee-shell" d="M10 82A80 80 0 0 1 170 82H10Z" />
                    <path class="bee-inner" d="M58 82A32 32 0 0 1 122 82H58Z" />
                    <path class="bee-baseline" d="M10 82H170" />
                    <text class="is-filled" x="35" y="66">★</text>
                    <text class="is-filled" x="58" y="42">★</text>
                    <text class="is-filled" x="90" y="32">★</text>
                    <text class="is-empty" x="122" y="42">☆</text>
                    <text class="is-empty" x="145" y="66">☆</text>
                    <text class="bee-label" x="90" y="74">3 STAR</text>
                  </svg>
                </span>
              </dd>
            </div>
            <div><dt>Recommended room size</dt><dd>Up to 75 sq. ft.</dd></div>
            <div><dt>Socket requirement</dt><dd>Standard 6 Amp plug</dd></div>
          </dl>
        </div>
      </section>

  @include('website.partials.meta-view-content', ['configKey' => 'studio_ac'])
@endsection
