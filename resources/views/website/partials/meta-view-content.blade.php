{{--
  Fires Meta ViewContent on product detail pages.
  Usage: @include('website.partials.meta-view-content', ['configKey' => 'studio_ac'])
--}}
@php
    $metaViewContent = \App\Services\Meta\MetaProductPayload::fromConfigKey($configKey);
@endphp

@if (config('meta.enable_pixel') && $metaViewContent)
  @push('meta_pixel_events')
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        window.oakterMeta?.trackViewContent(@json($metaViewContent));
      });
    </script>
  @endpush
@endif
