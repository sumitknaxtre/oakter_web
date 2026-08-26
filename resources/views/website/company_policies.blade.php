@extends('layouts.website')

@section('title', 'Company Policies | Oakter')
@section('meta_description', "Read Oakter's Company Policies, including the Corporate Social Responsibility (CSR) Policy and the Prevention of Sexual Harassment (POSH) Policy.")
@section('canonical', route('website.company_policies'))

@section('structured_data')
    <script type="application/ld+json">{"@@context":"https://schema.org","@@type":"Organization","name":"Oakter","url":"https://www.oakter.com","logo":"{{ asset('assets/oakter-logo-1200.png') }}","sameAs":["https://www.instagram.com/oyeoakter/","https://www.facebook.com/oakter/","https://www.youtube.com/channel/UC3h_V9-78yWVbtTi5eNWvZQ"],"contactPoint":[{"@@type":"ContactPoint","telephone":"+91-75750-40506","contactType":"customer support","areaServed":"IN"}]}</script>
    <script type="application/ld+json">{"@@context":"https://schema.org","@@type":"BreadcrumbList","itemListElement":[{"@@type":"ListItem","position":1,"name":"Home","item":"https://www.oakter.com/"},{"@@type":"ListItem","position":2,"name":"Company Policies","item":"{{ route('website.company_policies') }}"}]}</script>
@endsection

@section('content')
      <section class="about-hero privacy-hero">
        <div>
          <p class="eyebrow">Company policies</p>
          <h1>Company Policies</h1>
          <p>
            Official Riot Labz Private Limited (Oakter) policies. Expand a section below to read
            the CSR Policy or the POSH Policy in full.
          </p>
        </div>
      </section>

      <section class="privacy-section company-policies-section">
        <div class="policy-accordion">
          <details class="policy-accordion-item">
            <summary>
              <span class="policy-accordion-title">CSR Policy</span>
            </summary>
            <div class="policy-document">
              @include('website.partials.policies.csr_policy_content')
            </div>
          </details>

          <details class="policy-accordion-item">
            <summary>
              <span class="policy-accordion-title">POSH Policy</span>
            </summary>
            <div class="policy-document">
              @include('website.partials.policies.posh_policy_content')
            </div>
          </details>
        </div>
      </section>
@endsection
