<section class="checkout-hero" aria-label="{{ $product['aria_label'] }}">
  <div @class(['checkout-image', 'checkout-image-dissolve' => $product['image_dissolve']])>
    <img src="{{ asset($product['images']['hero']) }}" alt="{{ $product['images']['hero_alt'] }}" />
    @if ($product['image_dissolve'])
      <img src="{{ asset($product['images']['dissolve']) }}" alt="{{ $product['images']['dissolve_alt'] }}" />
    @endif
  </div>
  <div class="checkout-panel">
    <img class="checkout-thumb" src="{{ asset($product['images']['thumb']) }}" alt="{{ $product['images']['thumb_alt'] }}" />
    <p class="eyebrow">{{ $product['eyebrow'] }}</p>
    <h1>{{ $product['headline'] }}</h1>
    <p>{{ $product['lede'] }}</p>
    @if ($product['note'])
      <p class="checkout-note">{{ $product['note'] }}</p>
    @endif
    <div class="checkout-summary" aria-label="Order summary">
      @foreach ($product['summary'] as $row)
        <div>
          <span>{{ $row['label'] }}</span>
          <strong>{!! $row['value'] !!}</strong>
        </div>
      @endforeach
    </div>
    @if (session('product_unavailable'))
      <p class="checkout-unavailable" role="alert">{{ session('product_unavailable') }} is currently out of stock.</p>
    @endif
    <div class="checkout-actions">
      @include('website.partials.product-buy-button', [
        'inStock' => $product['is_in_stock'] ?? true,
        'hideBuyButton' => $product['hide_buy_button'] ?? false,
        'href' => route('website.checkout.show', ['product' => $productSlug]),
        'label' => 'Proceed to payment',
      ])
    </div>
  </div>
</section>
