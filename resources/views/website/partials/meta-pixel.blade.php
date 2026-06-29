{{--
  Meta Pixel base code — loads on every customer-facing page.
  PageView is fired automatically on init (Meta recommended default).
--}}
@if (config('meta.enable_pixel') && config('meta.pixel_id'))
  <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', @json(config('meta.pixel_id')));
    fbq('track', 'PageView');
  </script>
  <noscript>
    <img
      height="1"
      width="1"
      style="display:none"
      src="https://www.facebook.com/tr?id={{ urlencode(config('meta.pixel_id')) }}&ev=PageView&noscript=1"
      alt=""
    />
  </noscript>
@endif
