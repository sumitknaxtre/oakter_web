@extends('layouts.admin')

@section('title', 'Add news article | Oakter Admin')

@section('content')
  <div class="admin-topbar">
    <div>
      <h1>Add news article</h1>
      <p>Create a press item for the Media Insights page.</p>
    </div>
    <a class="admin-link-button secondary" href="{{ route('admin.news-articles.index') }}">Back to articles</a>
  </div>

  <section class="admin-panel" style="padding: 20px;">
    <form class="admin-form admin-form-wide" method="post" action="{{ route('admin.news-articles.store') }}" enctype="multipart/form-data">
      @csrf
      @include('admin.news_articles._form')
      <button class="admin-button" type="submit">Create article</button>
    </form>
  </section>
@endsection
