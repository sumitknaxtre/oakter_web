@extends('layouts.website')

@section('title', 'Contact Oakter | Customer Care and Facilities')
@section('meta_description', 'Contact Oakter customer care and find Oakter facility addresses in Noida and Alwar.')
@section('canonical', route('website.contact'))

@section('structured_data')
    <script type="application/ld+json">{"@@context":"https://schema.org","@@type":"Organization","name":"Oakter","url":"https://www.oakter.com","logo":"{{ asset('assets/oakter-logo-1200.png') }}","sameAs":["https://www.instagram.com/oyeoakter/","https://www.facebook.com/oakter/","https://www.youtube.com/channel/UC3h_V9-78yWVbtTi5eNWvZQ"],"contactPoint":[{"@@type":"ContactPoint","telephone":"+91-75750-40506","contactType":"customer support","areaServed":"IN"}]}</script>
    <script type="application/ld+json">{"@@context":"https://schema.org","@@type":"LocalBusiness","name":"Oakter","url":"https://www.oakter.com","telephone":"+91-75750-40506","address":[{"@@type":"PostalAddress","streetAddress":"A-21, Phase-2","addressLocality":"Noida","addressRegion":"Uttar Pradesh","postalCode":"201305","addressCountry":"IN"},{"@@type":"PostalAddress","streetAddress":"B-36, M.I.A, Matsya Industrial Area","addressLocality":"Alwar","addressRegion":"Rajasthan","postalCode":"301030","addressCountry":"IN"}]}</script>
    <script type="application/ld+json">{"@@context":"https://schema.org","@@type":"BreadcrumbList","itemListElement":[{"@@type":"ListItem","position":1,"name":"Home","item":"https://www.oakter.com/"},{"@@type":"ListItem","position":2,"name":"Contact Oakter","item":"{{ route('website.contact') }}"}]}</script>
@endsection

@section('content')
      <section class="about-hero contact-hero">
        <div>
          <p class="eyebrow">Contact us</p>
          <h1>Support and facilities.</h1>
          <p>
            Reach Oakter customer care on WhatsApp, or find the Noida and Alwar facilities below.
          </p>
        </div>
      </section>

      <section class="contact-section">
        <div class="contact-shell">
          <article class="contact-card support-card">
            <span>Customer care</span>
            <h2>WhatsApp support</h2>
            <p>For product support and service questions.</p>
            <a class="contact-phone" href="tel:+917575040506">+91 75750-40506</a>
            <a class="button primary" href="https://wa.me/917575040506" target="_blank" rel="noopener">Open WhatsApp</a>
          </article>
          <article class="contact-card qr-card">
            <img src="data:image/svg+xml,%3c?xml%20version='1.0'%20encoding='utf-8'?%3e%3c!DOCTYPE%20svg%20PUBLIC%20'-//W3C//DTD%20SVG%201.1//EN'%20'http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd'%3e%3csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%2033%2033'%20shape-rendering='crispEdges'%3e%3cpath%20fill='%23ffffff'%20d='M0%200h33v33H0z'/%3e%3cpath%20stroke='%23000000'%20d='M4%204.5h7m4%200h3m1%200h1m2%200h7M4%205.5h1m5%200h1m6%200h2m1%200h1m1%200h1m5%200h1M4%206.5h1m1%200h3m1%200h1m1%200h1m1%200h7m1%200h1m1%200h3m1%200h1M4%207.5h1m1%200h3m1%200h1m1%200h3m3%200h3m1%200h1m1%200h3m1%200h1M4%208.5h1m1%200h3m1%200h1m1%200h2m5%200h1m2%200h1m1%200h3m1%200h1M4%209.5h1m5%200h1m1%200h2m1%200h3m2%200h1m1%200h1m5%200h1M4%2010.5h7m1%200h1m1%200h1m1%200h1m1%200h1m1%200h1m1%200h7M12%2011.5h1m4%200h1M4%2012.5h1m1%200h5m2%200h1m1%200h1m3%200h2m1%200h5M6%2013.5h2m1%200h1m1%200h2m3%200h1m6%200h1m3%200h1M7%2014.5h7m1%200h1m1%200h3m2%200h4m1%200h2M6%2015.5h1m6%200h3m1%200h3m1%200h3m4%200h1M5%2016.5h3m2%200h2m3%200h6m1%200h3m1%200h3M4%2017.5h5m8%200h1m1%200h1m3%200h1m1%200h1m1%200h1M4%2018.5h1m1%200h3m1%200h1m2%200h4m2%200h3m1%200h3m1%200h2M4%2019.5h1m1%200h3m2%200h1m1%200h3m4%200h1m2%200h2m3%200h1M4%2020.5h1m2%200h1m1%200h2m1%200h1m2%200h1m4%200h5m1%200h1M12%2021.5h1m1%200h1m1%200h2m1%200h2m3%200h2M4%2022.5h7m6%200h2m1%200h1m1%200h1m1%200h1m1%200h3M4%2023.5h1m5%200h1m1%200h1m3%200h2m1%200h2m3%200h2M4%2024.5h1m1%200h3m1%200h1m1%200h1m1%200h1m1%200h1m1%200h7m1%200h2M4%2025.5h1m1%200h3m1%200h1m1%200h1m1%200h1m6%200h2m1%200h3m1%200h1M4%2026.5h1m1%200h3m1%200h1m1%200h1m2%200h2m8%200h2m1%200h1M4%2027.5h1m5%200h1m4%200h1m2%200h4m1%200h3m2%200h1M4%2028.5h7m1%200h2m1%200h1m5%200h8'/%3e%3c/svg%3e" alt="WhatsApp support QR for Oakter customer care" />
            <span>Scan to chat</span>
          </article>
          <div class="facility-stack" aria-label="Oakter facility addresses">
            <article>
              <div>
                <h3>Noida</h3>
                <p>D-57, Sector-6, Noida, Uttar Pradesh 201301</p>
                <p>A-21, Phase-2, Noida, Uttar Pradesh 201305</p>
              </div>
              <a
                class="waypoint-link"
                href="https://www.google.com/maps/search/?api=1&query=28.533602286175977,77.39980280978833"
                target="_blank"
                rel="noopener"
                aria-label="Open A-21 Noida waypoint in Google Maps"
              >
                <span></span>
              </a>
            </article>
            <article>
              <div>
                <h3>Alwar</h3>
                <p>B-36, M.I.A, Matsya Industrial Area, Alwar, Rajasthan 301030</p>
              </div>
              <a
                class="waypoint-link"
                href="https://www.google.com/maps/search/?api=1&query=27.51944331948311,76.6832234789717"
                target="_blank"
                rel="noopener"
                aria-label="Open Alwar waypoint in Google Maps"
              >
                <span></span>
              </a>
            </article>
          </div>
        </div>
      </section>
@endsection
