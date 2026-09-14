@extends('layouts.website')

@section('title', 'Find a Studio AC Retail Outlet | Oakter')
@section('meta_description', 'Find a retail outlet to purchase Oakter Studio AC in your city. Select your state or UT to see nearby dealers.')
@section('canonical', route('website.retail_outlets'))

@section('content')
  <section class="retail-outlets-page" data-retail-outlets-page>
    <div class="retail-outlet-top">
      <div class="retail-outlet-hero-copy">
        <p class="eyebrow">Studio AC retail network</p>
        <h1>Find a Studio AC store near you.</h1>
      </div>

      <div class="store-finder" data-store-finder data-dealers-url="{{ $dealersUrl }}">
        <div class="store-finder-panel">
          <label class="visually-hidden" for="store-state">Choose your region</label>
          <div class="store-finder-select-wrap">
            <select id="store-state" name="state" data-store-state>
              <option value="">Choose your region</option>
              @foreach ($states as $state)
                <option value="{{ $state }}">{{ $state }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
    </div>

    <div class="outlet-directory" id="store-directory" data-outlet-directory aria-live="polite">
      <div class="outlet-directory-heading">
        <h2 data-outlet-title>Choose a region to see stores.</h2>
        <p data-outlet-message>Store addresses will appear here as soon as you select a region.</p>
      </div>
      <div class="outlet-directory-list" data-outlet-list></div>
    </div>
  </section>
@endsection
