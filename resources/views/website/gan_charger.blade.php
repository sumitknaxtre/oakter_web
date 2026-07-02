@extends('layouts.website')

@php($ganCharger = \App\Support\ProductCatalog::forConfigKey('gan_charger'))

@section('title', 'Oakter 65W GaN Charger | Fast USB-C Charger')
@section('meta_description', 'Shop Oakter 65W GaN charger for phones, tablets, earbuds, power banks and USB-C laptops.')
@section('canonical', route('website.gan_charger'))
@section('og_type', 'product')
@section('og_image', asset('assets/gan-charger-with-cable-900.jpg'))

@section('structured_data')
    <script type="application/ld+json">{"@@context":"https://schema.org","@@type":"Organization","name":"Oakter","url":"https://www.oakter.com","logo":"{{ asset('assets/oakter-logo-1200.png') }}","sameAs":["https://www.instagram.com/oyeoakter/","https://www.facebook.com/oakter/","https://www.youtube.com/channel/UC3h_V9-78yWVbtTi5eNWvZQ"],"contactPoint":[{"@@type":"ContactPoint","telephone":"+91-75750-40506","contactType":"customer support","areaServed":"IN"}]}</script>
    <script type="application/ld+json">{"@@context":"https://schema.org","@@type":"Product","name":"Oakter 65W GaN Charger","brand":{"@@type":"Brand","name":"Oakter"},"description":"Compact 65W USB-C GaN fast charger for phones, tablets and laptops.","image":"{{ asset('assets/gan-charger-with-cable-900.jpg') }}","offers":{"@@type":"Offer","priceCurrency":"INR","price":"1399","availability":"https://schema.org/InStock","url":"{{ route('website.gan_charger') }}"}}</script>
    <script type="application/ld+json">{"@@context":"https://schema.org","@@type":"BreadcrumbList","itemListElement":[{"@@type":"ListItem","position":1,"name":"Home","item":"https://www.oakter.com/"},{"@@type":"ListItem","position":2,"name":"Oakter 65W GaN Charger","item":"{{ route('website.gan_charger') }}"}]}</script>
@endsection

@section('content')
      <section class="product-band page-hero">
        <div class="product-copy">
          <p class="eyebrow">GaN Charger</p>
          <h1>One compact charger for phone, tablet and laptop.</h1>
          <p>
            Oakter 65W GaN charger is the everyday fast charger for modern USB-C devices, designed for
            travel, work desks and fewer charging bricks.
          </p>
        </div>
        <img
          src="{{ asset('assets/gan-charger-with-cable-900.jpg') }}"
          alt="Oakter GaN charger with cable"
        />
        <div class="product-buy-bar">
          <div class="price-row">
            <strong>{{ $ganCharger['listing']['price'] }}</strong>
            <span>MRP {{ $ganCharger['listing']['mrp'] }}</span>
            <em>{{ $ganCharger['listing']['tagline'] }}</em>
          </div>
          <div class="marketplaces compact">
            <strong>Available on</strong>
            <a href="https://www.flipkart.com/oakter-65w-fast-gan-charger-usb-adapter/p/itmebbb85408cce9?pid=USBHESU4XCDHZ7PN&lid=LSTUSBHESU4XCDHZ7PNLIPTQ8&marketplace=FLIPKART&q=oakter&store=search.flipkart.com&srno=s_1_2&otracker=search&otracker1=search&fm=search-autosuggest&iid=en_ZosSRDX0Pom0PAsQeVRr5YofopOChXV9q3jTGfuIamfU4e6VuPI8MkyGHwdalaziU31JUcPlbIypv292fLEu6RfyJcVZGsXRbHRVe0DtUVZev4ItooSyfw7uskF_ayIa&ppt=sp&ppn=sp&ssid=l07racsydc0000001781865946534&qH=845ce83d7b58e53a&ov_redirect=true&ov_redirect=true" aria-label="Buy Oakter GaN charger on Flipkart">
              <img src="{{ asset('assets/mp-flipkart-B4UvsN8l.webp') }}" alt="Flipkart" />
            </a>
            <a href="https://www.amazon.in/dp/B0FJMGV96Q?th=1" aria-label="Buy Oakter GaN charger on Amazon">
              <img src="{{ asset('assets/mp-amazon-DztdINrT.png') }}" alt="Amazon" />
            </a>
            <span class="marketplace-disabled" aria-label="Blinkit not available for Oakter GaN charger">
              <img src="{{ asset('assets/mp-blinkit-CY2t1LLC.png') }}" alt="Blinkit" />
            </span>
          </div>
          @include('website.partials.product-buy-button', [
            'inStock' => $ganCharger['is_in_stock'] ?? true,
            'hideBuyButton' => $ganCharger['hide_buy_button'] ?? false,
            'href' => route('website.buy_gan_charger'),
            'label' => 'BUY',
          ])
        </div>
      </section>

      <div class="carousel-shell feature-carousel-shell">
        <section class="feature-grid" data-carousel>
          <article><span>01</span><h3>65W output</h3><p>Supports phones, tablets, earbuds, power banks and USB-C laptops that accept up to 65W charging.</p></article>
          <article><span>02</span><h3>Compact GaN</h3><p>Modern GaN design keeps the charger smaller and easier to carry than older laptop adapters.</p></article>
          <article><span>03</span><h3>Travel ready</h3><p>One charger for work desks, travel bags and everyday device charging at home.</p></article>
        </section>
        <div class="carousel-dots" aria-label="GaN charger feature slides"></div>
      </div>

  @include('website.partials.meta-view-content', ['configKey' => 'gan_charger'])
@endsection
