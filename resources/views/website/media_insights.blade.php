@extends('layouts.website')

@section('title', 'Media Insights | Oakter in Press')
@section('meta_description', 'Read news, reviews and press coverage about Oakter products including Mini UPS, Studio AC and GaN chargers.')
@section('canonical', route('website.media_insights'))

@section('structured_data')
    <script type="application/ld+json">{"@@context":"https://schema.org","@@type":"Organization","name":"Oakter","url":"https://www.oakter.com","logo":"{{ asset('assets/oakter-logo-1200.png') }}"}</script>
    <script type="application/ld+json">{"@@context":"https://schema.org","@@type":"BreadcrumbList","itemListElement":[{"@@type":"ListItem","position":1,"name":"Home","item":"https://www.oakter.com/"},{"@@type":"ListItem","position":2,"name":"Media Insights","item":"{{ route('website.media_insights') }}"}]}</script>
@endsection

@section('content')
      <section class="about-hero media-insights-hero">
        <div>
          <p class="eyebrow">Media insights</p>
          <h1>Oakter in Press</h1>
          <p>
            News, reviews and press releases covering Oakter products, launches and milestones.
          </p>
        </div>
      </section>

      <section class="section media-insights-section" aria-label="News and press releases">
        <div class="section-heading">
          <p class="eyebrow">News &amp; Press Releases</p>
          <h2>Latest coverage</h2>
        </div>

        @if ($articles->isEmpty())
          <p class="media-press-empty">New coverage will appear here soon.</p>
        @else
          <div class="media-press-list">
            @foreach ($articles as $article)
              <article>
                @if ($article->imageUrl())
                  <div class="media-press-image">
                    <img src="{{ $article->imageUrl() }}" alt="{{ $article->title }}" loading="lazy" />
                  </div>
                @endif
                <time datetime="{{ $article->published_at->toIso8601String() }}">{{ $article->formattedPublishedDate() }}</time>
                <div>
                  <h3>
                    @if ($article->url)
                      <a href="{{ $article->url }}" target="_blank" rel="noopener noreferrer">
                        {{ $article->title }}
                      </a>
                    @else
                      {{ $article->title }}
                    @endif
                  </h3>
                  @if ($article->source)
                    <p>{{ $article->source }}</p>
                  @endif
                </div>
              </article>
            @endforeach
          </div>
        @endif
      </section>
@endsection
