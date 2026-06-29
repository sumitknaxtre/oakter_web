@extends('layouts.website')

@php
    $miniUps = \App\Support\ProductCatalog::forConfigKey('mini_ups');
    $miniUpsAirfiber = \App\Support\ProductCatalog::forConfigKey('mini_ups_airfiber');
@endphp

@section('title', 'Oakter Mini UPS | Router & AirFiber Power Backup')
@section('meta_description', 'Shop Oakter Mini UPS products for Wi-Fi routers, AirFiber devices, CCTV and set-top boxes during short power outages.')
@section('canonical', route('website.mini_ups'))
@section('og_type', 'product')
@section('og_title', 'Oakter Mini UPS | Router & AirFiber Power Backup')

@section('structured_data')
    <script type="application/ld+json">{"@@context":"https://schema.org","@@type":"Organization","name":"Oakter","url":"https://www.oakter.com","logo":"{{ asset('assets/oakter-logo-1200.png') }}","sameAs":["https://www.instagram.com/oyeoakter/","https://www.facebook.com/oakter/","https://www.youtube.com/channel/UC3h_V9-78yWVbtTi5eNWvZQ"],"contactPoint":[{"@@type":"ContactPoint","telephone":"+91-75750-40506","contactType":"customer support","areaServed":"IN"}]}</script>
    <script type="application/ld+json">{"@@context":"https://schema.org","@@type":"Product","name":"Oakter Mini UPS","brand":{"@@type":"Brand","name":"Oakter"},"description":"Router and AirFiber power backup for short power outages.","image":"{{ asset('assets/oakter-logo-1200.png') }}","offers":{"@@type":"AggregateOffer","priceCurrency":"INR","lowPrice":"949","highPrice":"2899","availability":"https://schema.org/InStock","url":"{{ route('website.mini_ups') }}"}}</script>
    <script type="application/ld+json">{"@@context":"https://schema.org","@@type":"BreadcrumbList","itemListElement":[{"@@type":"ListItem","position":1,"name":"Home","item":"https://www.oakter.com/"},{"@@type":"ListItem","position":2,"name":"Oakter Mini UPS","item":"{{ route('website.mini_ups') }}"}]}</script>
@endsection

@section('content')
      <section class="product-band ups-hero page-hero">
        <img
          src="{{ asset('assets/mini-ups-plus-hero-BR3VVkkM.jpg') }}"
          alt="Oakter Mini UPS Plus connected to a Wi-Fi router"
        />
        <div class="product-copy">
          <p class="eyebrow">Mini UPS</p>
          <h1>Keep Wi-Fi running when power drops.</h1>
          <p>
            Oakter Mini UPS products provide simple backup for routers, AirFiber devices, CCTV and
            set-top boxes so work, classes, streaming and payments do not stop during short power outages.
          </p>
          <p>The choice for routers that operate on 12VDC.</p>
        </div>
        <div class="product-buy-bar">
          <div class="price-row">
            <strong>{{ $miniUps['listing']['price'] }}</strong>
            <span>MRP {{ $miniUps['listing']['mrp'] }}</span>
            <em>{{ $miniUps['listing']['tagline'] }}</em>
          </div>
          <div class="marketplaces compact">
            <strong>Available on</strong>
            <a href="https://www.flipkart.com/oakter-mini-ups-12v-wi-fi-power-backup-router/p/itm7c56ad59804e7?pid=MNUFWCNYFHEV25FA&lid=LSTMNUFWCNYFHEV25FALRX1IT&marketplace=FLIPKART&q=mini+ups&store=6bo%2F70k%2Flpq&srno=s_1_7&otracker=search&otracker1=search&fm=organic&iid=732fcfa8-3690-4105-8f2e-e9bb926eed40.MNUFWCNYFHEV25FA.SEARCH&ppt=None&ppn=None&ssid=xwzy62hqtc0000001781865851313&qH=d6c0080a896ea335&ov_redirect=true&ov_redirect=true" aria-label="Buy Oakter Mini UPS on Flipkart">
              <img src="{{ asset('assets/mp-flipkart-B4UvsN8l.webp') }}" alt="Flipkart" />
            </a>
            <a href="https://www.amazon.in/Mini-UPS-Router-WiFi-12V/dp/B08HLZ28QC?ref_=ast_bl_cpl_dp" aria-label="Buy Oakter Mini UPS on Amazon">
              <img src="{{ asset('assets/mp-amazon-DztdINrT.png') }}" alt="Amazon" />
            </a>
            <a href="https://blinkit.com/prn/oakter-mini-12-v-wifi-router-ups/prid/567290" aria-label="Buy Oakter Mini UPS on Blinkit">
              <img src="{{ asset('assets/mp-blinkit-CY2t1LLC.png') }}" alt="Blinkit" />
            </a>
          </div>
          @include('website.partials.product-buy-button', [
            'inStock' => $miniUps['is_in_stock'] ?? true,
            'href' => route('website.buy_mini_ups'),
            'label' => 'BUY',
          ])
        </div>
      </section>

      <section class="product-band product-spotlight">
        <div class="product-copy">
          <p class="eyebrow">Mini UPS for AirFiber</p>
          <h2>Your airfiber router's personal power backup.</h2>
          <p>
            Built for AirFiber and broadband devices that need simple, reliable short term power
            backup when the main supply drops for upto 4 hours depending on your router model.
          </p>
        </div>
        <img
          src="{{ asset('assets/mini-ups-airfiber-Bq0UZAod.png') }}"
          alt="Oakter Mini UPS for AirFiber"
        />
        <div class="product-buy-bar">
          <div class="price-row">
            <strong>{{ $miniUpsAirfiber['listing']['price'] }}</strong>
            <span>MRP {{ $miniUpsAirfiber['listing']['mrp'] }}</span>
            <em>{{ $miniUpsAirfiber['listing']['tagline'] }}</em>
          </div>
          <div class="marketplaces compact">
            <strong>Available on</strong>
            <a href="https://www.flipkart.com/product/p/itme?pid=MNUHHYDDDB8XHHVX" aria-label="Buy Mini UPS for AirFiber on Flipkart">
              <img src="{{ asset('assets/mp-flipkart-B4UvsN8l.webp') }}" alt="Flipkart" />
            </a>
            <a href="https://www.amazon.in/dp/B0FWK6YFCW" aria-label="Buy Mini UPS for AirFiber on Amazon">
              <img src="{{ asset('assets/mp-amazon-DztdINrT.png') }}" alt="Amazon" />
            </a>
            <a href="https://blinkit.com/prn/oakter-v3.0-mini-ups-for-airfiber/prid/729497" aria-label="Buy Mini UPS for AirFiber on Blinkit">
              <img src="{{ asset('assets/mp-blinkit-CY2t1LLC.png') }}" alt="Blinkit" />
            </a>
          </div>
          @include('website.partials.product-buy-button', [
            'inStock' => $miniUpsAirfiber['is_in_stock'] ?? true,
            'href' => route('website.buy_mini_ups_airfiber'),
            'label' => 'BUY',
          ])
        </div>
      </section>

  @include('website.partials.meta-view-content', ['configKey' => 'mini_ups'])
  @include('website.partials.meta-view-content', ['configKey' => 'mini_ups_airfiber'])
@endsection
