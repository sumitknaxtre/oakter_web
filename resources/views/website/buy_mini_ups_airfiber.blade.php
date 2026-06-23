@extends('layouts.website')

@php($product = \App\Support\ProductCatalog::forConfigKey('mini_ups_airfiber'))

@section('title', $product['title'])
@section('meta_description', $product['description'])
@section('canonical', route('website.buy_mini_ups_airfiber'))
@section('og_title', $product['title'])
@section('robots', 'noindex, nofollow')

@section('content')
  @include('website.partials.checkout', ['product' => $product, 'productSlug' => 'mini-ups-airfiber'])
@endsection
