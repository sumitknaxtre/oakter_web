{{--
  Google Analytics 4 base tag — loads on every customer-facing page.
  Page views fire automatically via gtag('config', ...).
--}}
@if (config('analytics.enabled') && config('analytics.measurement_id'))
  <script async src="https://www.googletagmanager.com/gtag/js?id={{ urlencode(config('analytics.measurement_id')) }}"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', @json(config('analytics.measurement_id')));
  </script>
@endif
