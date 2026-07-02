@props([
    'inStock' => true,
    'hideBuyButton' => false,
    'href' => '#',
    'label' => 'BUY',
])

@if ($hideBuyButton)
@elseif ($inStock)
  <a class="button primary" href="{{ $href }}">{{ $label }}</a>
@else
  <span class="button primary is-disabled" aria-disabled="true">Out of Stock</span>
@endif
