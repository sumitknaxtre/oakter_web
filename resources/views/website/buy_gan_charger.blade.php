@extends('layouts.website')

@php($product = \App\Support\ProductCatalog::forConfigKey('gan_charger'))

@section('title', $product['title'])
@section('meta_description', $product['description'])
@section('canonical', route('website.buy_gan_charger'))
@section('og_title', $product['title'])
@section('og_image', asset($product['images']['hero']))
@section('robots', 'noindex, nofollow')

@section('content')
  @include('website.partials.checkout', ['product' => $product, 'productSlug' => 'gan-charger'])
@endsection
