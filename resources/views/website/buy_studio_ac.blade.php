@extends('layouts.website')

@php($product = \App\Support\ProductCatalog::forConfigKey('studio_ac'))

@section('title', $product['title'])
@section('meta_description', $product['description'])
@section('canonical', route('website.buy_studio_ac'))
@section('og_title', $product['title'])
@section('robots', 'noindex, nofollow')

@section('content')
  @include('website.partials.checkout', ['product' => $product, 'productSlug' => 'studio-ac'])
@endsection
