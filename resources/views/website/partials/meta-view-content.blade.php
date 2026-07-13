{{--
  Fires Meta ViewContent and GA4 view_item on product detail pages.
  Usage: @include('website.partials.meta-view-content', ['configKey' => 'studio_ac'])
--}}
@php
    $metaViewContent = \App\Services\Meta\MetaProductPayload::fromConfigKey($configKey);
@endphp

@if ($metaViewContent)
  @if (config('meta.enable_pixel'))
    @push('meta_pixel_events')
      <script>
        document.addEventListener('DOMContentLoaded', function () {
          window.oakterMeta?.trackViewContent(@json($metaViewContent));
        });
      </script>
    @endpush
  @endif

  @if (config('analytics.enabled'))
    @push('ga_events')
      <script>
        document.addEventListener('DOMContentLoaded', function () {
          window.oakterGa?.trackViewItem(@json($metaViewContent));
        });
      </script>
    @endpush
  @endif
@endif
