@extends('layouts.website')

@section('title', 'Oakter B2B Products | EMS, ODM and Manufacturing in India')
@section('meta_description', 'Talk to Oakter for B2B manufacturing, EMS, ODM, payment devices, energy meters, smart home products and consumer electronics programs.')
@section('canonical', route('website.b2b'))
@section('og_title', 'Oakter B2B Products | EMS, ODM and Manufacturing in India')

@section('structured_data')
    <script type="application/ld+json">{"@@context":"https://schema.org","@@type":"Organization","name":"Oakter","url":"https://www.oakter.com","logo":"{{ asset('assets/oakter-logo-1200.png') }}","sameAs":["https://www.instagram.com/oyeoakter/","https://www.facebook.com/oakter/","https://www.youtube.com/channel/UC3h_V9-78yWVbtTi5eNWvZQ"],"contactPoint":[{"@@type":"ContactPoint","telephone":"+91-75750-40506","contactType":"customer support","areaServed":"IN"}]}</script>
    <script type="application/ld+json">{"@@context":"https://schema.org","@@type":"ProfessionalService","name":"Oakter B2B Products","url":"{{ route('website.b2b') }}","provider":{"@@type":"Organization","name":"Oakter","url":"https://www.oakter.com"},"areaServed":"India","serviceType":["EMS manufacturing","ODM services","B2B electronics manufacturing"]}</script>
    <script type="application/ld+json">{"@@context":"https://schema.org","@@type":"BreadcrumbList","itemListElement":[{"@@type":"ListItem","position":1,"name":"Home","item":"https://www.oakter.com/"},{"@@type":"ListItem","position":2,"name":"Oakter B2B Products","item":"{{ route('website.b2b') }}"}]}</script>
@endsection

@section('content')
      <section class="product-band b2b-product">
        <div class="b2b-visual">
          <img class="sony-kit-image" src="{{ asset('assets/sony-smart-home-kit-BZVD6ajK.png') }}" alt="Oakter smart home kit packaged for Sony" />
          <a class="b2b-logo-link" href="https://www.sony.co.in/" target="_blank" rel="noopener" aria-label="Visit Sony website">
            <img src="{{ asset('assets/logo-sony-BDALeJ-y.png') }}" alt="Sony" />
          </a>
        </div>
        <div class="product-copy">
          <p class="eyebrow">Smart home products</p>
          <h2>SONY</h2>
          <p>
            Sony launched their Google TVs with an Oakter smart home plug bundled with them, bringing
            simple connected-home control into the TV purchase experience.
          </p>
          <a class="b2b-brand-link" href="https://www.sony.co.in/" target="_blank" rel="noopener">sony.co.in</a>
        </div>
      </section>

      <section class="product-band b2b-product">
        <div class="b2b-image-slider" aria-label="Paytm Soundbox product images">
          <img src="{{ asset('assets/paytm-soundbox-01-Dbab2h-1.png') }}" alt="Paytm Soundbox with QR display" />
          <img src="{{ asset('assets/paytm-soundbox-02-DupPdQJJ.png') }}" alt="Paytm Soundbox device front and back" />
          <a class="b2b-logo-link" href="https://paytm.com/" target="_blank" rel="noopener" aria-label="Visit Paytm website">
            <img src="data:image/png;base64,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" alt="Paytm" />
          </a>
        </div>
        <div class="product-copy">
          <p class="eyebrow">Payment devices</p>
          <h2>Paytm Soundbox</h2>
          <p>
            Reliable payment announcement devices built for retail counters, service points and high-volume
            merchant deployments where uptime, clarity and consistency matter every day.
          </p>
          <a class="b2b-brand-link" href="https://paytm.com/" target="_blank" rel="noopener">paytm.com</a>
        </div>
      </section>

      <section class="product-band b2b-product">
        <div class="b2b-visual">
          <img class="energy-meter-image" src="{{ asset('assets/energy-meter-eGxbSoRF.png') }}" alt="Oakter energy meter products" />
          <a class="b2b-logo-link" href="https://www.oakter.com/" target="_blank" rel="noopener" aria-label="Visit Oakter website">
            <img src="{{ asset('assets/logo-oakter-DTvqQ746.png') }}" alt="Oakter" />
          </a>
        </div>
        <div class="product-copy">
          <p class="eyebrow">Energy intelligence</p>
          <h2>Energy Meter</h2>
          <p>
            Connected metering solutions for utility, facility and partner-led programs that need reliable
            measurement, efficient rollout support and dependable Indian manufacturing.
          </p>
          <a class="b2b-brand-link" href="https://www.oakter.com/" target="_blank" rel="noopener">oakter.com</a>
        </div>
      </section>

      <section class="product-band reverse b2b-product">
        <div class="b2b-visual">
          <img class="carvaan-image" src="{{ asset('assets/saregama-carvaan-DNPQB7Ae.png') }}" alt="Saregama Carvaan audio device" />
          <a class="b2b-logo-link" href="https://www.saregama.com/carvaan" target="_blank" rel="noopener" aria-label="Visit Saregama Carvaan website">
            <img src="{{ asset('assets/logo-carvaan-Bjqe_Wfq.png') }}" alt="Saregama Carvaan" />
          </a>
        </div>
        <div class="product-copy">
          <p class="eyebrow">Audio devices</p>
          <h2>Saregama Carvaan</h2>
          <p>
            Oakter designed and manufactured products for Saregama Carvaan, supporting audio devices
            that needed dependable product engineering, production quality and scaled execution.
          </p>
          <a class="b2b-brand-link" href="https://www.saregama.com/carvaan" target="_blank" rel="noopener">saregama.com/carvaan</a>
        </div>
      </section>

      <section class="product-band b2b-product">
        <div class="b2b-visual">
          <img class="portronics-image" src="{{ asset('assets/portronics-speaker-hG9RYBJ1.png') }}" alt="Portronics speaker and microphone" />
          <a class="b2b-logo-link" href="https://www.portronics.com/" target="_blank" rel="noopener" aria-label="Visit Portronics website">
            <img src="{{ asset('assets/logo-portronics-csdxUvZ5.png') }}" alt="Portronics" />
          </a>
        </div>
        <div class="product-copy">
          <p class="eyebrow">Audio devices</p>
          <h2>Portronics</h2>
          <p>
            Oakter manufactures for Portronics, supporting consumer audio and electronics programs
            with repeatable production, controlled assembly and dependable factory systems.
          </p>
          <a class="b2b-brand-link" href="https://www.portronics.com/" target="_blank" rel="noopener">portronics.com</a>
        </div>
      </section>

      <section class="product-band reverse b2b-product">
        <div class="b2b-visual">
          <img class="ambrane-image" src="{{ asset('assets/ambrane-product-YsPq1uo6.png') }}" alt="Ambrane consumer electronics product" />
          <a class="b2b-logo-link" href="https://www.ambraneindia.com/" target="_blank" rel="noopener" aria-label="Visit Ambrane website">
            <img src="{{ asset('assets/logo-ambrane-B3vlCCCd.png') }}" alt="Ambrane" />
          </a>
        </div>
        <div class="product-copy">
          <p class="eyebrow">Consumer electronics</p>
          <h2>Ambrane</h2>
          <p>
            Oakter manufactures for Ambrane, helping consumer electronics programs move through
            production with process control, reliability and Indian factory capability.
          </p>
          <a class="b2b-brand-link" href="https://www.ambraneindia.com/" target="_blank" rel="noopener">ambraneindia.com</a>
        </div>
      </section>

      <section class="product-band b2b-product">
        <div class="b2b-visual">
          <img class="curaa-image" src="{{ asset('assets/curaa-product-DOBcOzRg.png') }}" alt="Curaa consumer electronics product" />
          <a class="b2b-logo-link" href="https://curaahome.com/" target="_blank" rel="noopener" aria-label="Visit Curaa website">
            <img src="{{ asset('assets/logo-curaa-yC9wlCx5.png') }}" alt="Curaa" />
          </a>
        </div>
        <div class="product-copy">
          <p class="eyebrow">Consumer electronics</p>
          <h2>Curaa</h2>
          <p>
            Oakter manufactures for Curaa, supporting consumer electronics programs with dependable
            assembly, testing workflows and quality-focused production execution.
          </p>
          <a class="b2b-brand-link" href="https://curaahome.com/" target="_blank" rel="noopener">curaahome.com</a>
        </div>
      </section>

      <section class="product-band reverse b2b-product">
        <div class="b2b-visual">
          <img class="sabre-image" src="{{ asset('assets/sabre-product-BS76LE_j.png') }}" alt="Sabre personal safety products" />
          <a class="b2b-logo-link" href="https://www.sabrered.com/" target="_blank" rel="noopener" aria-label="Visit Sabre website">
            <img src="{{ asset('assets/logo-sabre-63-3VqNT.png') }}" alt="Sabre" />
          </a>
        </div>
        <div class="product-copy">
          <p class="eyebrow">Personal safety</p>
          <h2>Sabre</h2>
          <p>
            Oakter designs and manufactures personal safety products for Sabre, including connected
            safety devices such as Bluetooth pepper spray for modern personal security needs.
          </p>
          <a class="b2b-brand-link" href="https://www.sabrered.com/" target="_blank" rel="noopener">sabrered.com</a>
        </div>
      </section>



@endsection

