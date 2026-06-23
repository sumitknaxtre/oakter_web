@extends('layouts.website')

@section('title', 'About Oakter | Made in India Power and Smart Technology')
@section('meta_description', 'Learn about Oakter, an Indian technology brand building reliable power, cooling and smart products for India.')
@section('canonical', route('website.about'))
@section('og_title', 'About Oakter | Made in India Power and Smart Technology')

@section('structured_data')
    <script type="application/ld+json">{"@@context":"https://schema.org","@@type":"Organization","name":"Oakter","url":"https://www.oakter.com","logo":"{{ asset('assets/oakter-logo-1200.png') }}","sameAs":["https://www.instagram.com/oyeoakter/","https://www.facebook.com/oakter/","https://www.youtube.com/channel/UC3h_V9-78yWVbtTi5eNWvZQ"],"contactPoint":[{"@@type":"ContactPoint","telephone":"+91-75750-40506","contactType":"customer support","areaServed":"IN"}]}</script>
    <script type="application/ld+json">{"@@context":"https://schema.org","@@type":"BreadcrumbList","itemListElement":[{"@@type":"ListItem","position":1,"name":"Home","item":"https://www.oakter.com/"},{"@@type":"ListItem","position":2,"name":"About Oakter","item":"{{ route('website.about') }}"}]}</script>
@endsection

@section('content')
      <section class="about-hero">
        <div>
          <p class="eyebrow">Mission</p>
          <h1>#IndiaUninterrupted</h1>
          <p>
            Oakter's portfolio of products are designed specifically for the Indian user, helping improve
            everyday productivity and peace of mind.
          </p>
        </div>
      </section>

      <section class="about-video-section" aria-label="Oakter brand video">
        <div class="section-heading">
          <p class="eyebrow">Inside Oakter</p>
        </div>
        <div class="about-video-frame">
          <video
            src="{{ asset('assets/oakter-about-video.mp4') }}"
            controls
            playsinline="playsinline"
            preload="metadata"
            poster="{{ asset('assets/range-b2b-TIkhGcoA.jpg') }}"
          ></video>
        </div>
      </section>

      <section class="section about-factories">
        <div>
          <p class="eyebrow">Our factories</p>
          <h2>Advanced Indian ODM capability.</h2>
        </div>
        <div class="detail-columns">
          <article><h3>100K+ sq. ft.</h3><p>Factory area in Noida, India, focused on reliable energy and technology products.</p></article>
          <article><h3>25 FA lines</h3><p>Manufacturing capacity designed for repeatable quality and scaled product programs.</p></article>
          <article><h3>Class 10,000 clean room</h3><p>Controlled production environments for sensitive electronics and quality outcomes.</p></article>
          <article><h3>3 factories</h3><p>Facilities across D-57 and A-21 in Noida, plus B-36, M.I.A, Matsya Industrial Area, Alwar, Rajasthan 301030.</p></article>
        </div>
      </section>

      <section class="about-history">
        <div class="section-heading">
          <p class="eyebrow">History</p>
          <h2>Building For India, in India.</h2>
        </div>
        <div class="history-timeline">
          <article>
            <span>2015</span>
            <div>
              <h3>Oakter begins</h3>
              <p>
                The company starts with a focus on reliable, affordable technology products designed for
                Indian users and everyday Indian conditions.
              </p>
            </div>
          </article>
          <article>
            <span>Noida & Alwar</span>
            <div>
              <h3>Manufacturing base grows</h3>
              <p>
                Oakter's factories support energy, cooling and smart technology products with scaled
                manufacturing capacity and quality-controlled production.
              </p>
            </div>
          </article>
          <article>
            <span>EMS growth</span>
            <div>
              <h3>Built alongside India's electronics opportunity</h3>
              <p>
                India's electronics production nearly doubled between FY17 and FY22, with further growth
                projected at a compound annual growth rate of 24% from FY22 to FY27.
              </p>
            </div>
          </article>
          <article>
            <span>Today</span>
            <div>
              <h3>#IndiaUninterrupted</h3>
              <p>
                Oakter's portfolio of products are designed specifically for the Indian user, helping improve
                everyday productivity and peace of mind.
              </p>
            </div>
          </article>
        </div>
      </section>
@endsection
