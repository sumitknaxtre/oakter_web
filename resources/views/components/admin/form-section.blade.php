@props(['title'])

@if ($slot->isEmpty())
  <h3 {{ $attributes->class(['admin-form-section-heading']) }}>{{ $title }}</h3>
@else
  <div {{ $attributes->class(['admin-form-section']) }}>
    <h3 class="admin-form-section-heading">{{ $title }}</h3>
    {{ $slot }}
  </div>
@endif
