@extends('layouts.admin')

@section('title', 'Edit news article | Oakter Admin')

@section('content')
  <div class="admin-topbar">
    <div>
      <h1>Edit news article</h1>
      <p>Update press coverage shown on the Media Insights page.</p>
    </div>
    <a class="admin-link-button secondary" href="{{ route('admin.news-articles.index') }}">Back to articles</a>
  </div>

  <section class="admin-panel" style="padding: 20px;">
    <form class="admin-form admin-form-wide" method="post" action="{{ route('admin.news-articles.update', $article) }}" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      @include('admin.news_articles._form')
      <button class="admin-button" type="submit">Save changes</button>
    </form>
  </section>
@endsection
