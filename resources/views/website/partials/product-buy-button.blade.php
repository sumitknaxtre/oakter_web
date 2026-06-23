@props([
    'inStock' => true,
    'href' => '#',
    'label' => 'BUY',
])

@if ($inStock)
  <a class="button primary" href="{{ $href }}">{{ $label }}</a>
@else
  <span class="button primary is-disabled" aria-disabled="true">Out of Stock</span>
@endif
