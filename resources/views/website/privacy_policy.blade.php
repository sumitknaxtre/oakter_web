@extends('layouts.website')

@section('title', 'Oakter Privacy Policy')
@section('meta_description', "Read Oakter's privacy policy for information collection, use, disclosure, cookies, security and contact details.")
@section('canonical', route('website.privacy'))

@section('structured_data')
    <script type="application/ld+json">{"@@context":"https://schema.org","@@type":"Organization","name":"Oakter","url":"https://www.oakter.com","logo":"{{ asset('assets/oakter-logo-1200.png') }}","sameAs":["https://www.instagram.com/oyeoakter/","https://www.facebook.com/oakter/","https://www.youtube.com/channel/UC3h_V9-78yWVbtTi5eNWvZQ"],"contactPoint":[{"@@type":"ContactPoint","telephone":"+91-75750-40506","contactType":"customer support","areaServed":"IN"}]}</script>
    <script type="application/ld+json">{"@@context":"https://schema.org","@@type":"BreadcrumbList","itemListElement":[{"@@type":"ListItem","position":1,"name":"Home","item":"https://www.oakter.com/"},{"@@type":"ListItem","position":2,"name":"Oakter Privacy Policy","item":"{{ route('website.privacy') }}"}]}</script>
@endsection

@section('content')
      <section class="about-hero privacy-hero">
        <div>
          <p class="eyebrow">Privacy policy</p>
          <h1>Your information and how Oakter uses it.</h1>
          <p>
            This page summarises how Riot Labz Private Limited, operating as Oakter, collects,
            uses, shares and protects personal information when you use Oakter websites and services.
          </p>
        </div>
      </section>

      <section class="privacy-section">
        <div class="policy-note">
          <span>Last updated</span>
          <strong>May 14, 2026</strong>
        </div>
        <div class="policy-grid">
          <article>
            <h2>Information we collect</h2>
            <p>
              Oakter may collect contact details, order and account information, delivery details,
              payment-related information, device data, usage information and customer support
              communications. Some data is provided directly by you, while some is collected through
              cookies, analytics and ecommerce service providers.
            </p>
          </article>
          <article>
            <h2>How we use information</h2>
            <p>
              Information is used to process orders, deliver products, provide support, manage
              accounts, improve services, personalise communications, detect fraud, meet legal
              obligations and send marketing where permitted.
            </p>
          </article>
          <article>
            <h2>Sharing and service providers</h2>
            <p>
              Oakter may share information with vendors and service providers such as ecommerce
              platforms, payment processors, shipping partners, analytics tools, marketing providers
              and professional advisers. Information may also be disclosed when required by law,
              during business transfers, or to protect Oakter, customers and others.
            </p>
          </article>
          <article>
            <h2>Cookies and analytics</h2>
            <p>
              The website uses cookies and similar technologies to operate the store, remember
              preferences, understand traffic, improve performance and support marketing. Browser
              settings may allow you to block or remove cookies, though some services may not work
              correctly without them.
            </p>
          </article>
          <article>
            <h2>Your choices</h2>
            <p>
              Depending on applicable law, you may have rights to access, correct, delete or restrict
              certain personal information, object to processing, withdraw consent, opt out of
              marketing, or appeal a privacy decision.
            </p>
          </article>
          <article>
            <h2>Security and retention</h2>
            <p>
              Oakter uses reasonable safeguards to protect personal information, but no system is
              perfectly secure. Information is retained as needed for services, legal requirements,
              dispute resolution, security and business purposes.
            </p>
          </article>
          <article>
            <h2>Children and international use</h2>
            <p>
              Oakter services are not intended for children. Information may be processed or stored
              outside your state, province or country, including where service providers operate.
            </p>
          </article>
          <article>
            <h2>Contact for privacy requests</h2>
            <p>
              Riot Labz Private Limited<br />
              A-21, Phase-2, Noida, Uttar Pradesh 201305<br />
              <a href="mailto:Rahul.kolay@oakter.com">Rahul.kolay@oakter.com</a>
            </p>
          </article>
        </div>
      </section>
@endsection
