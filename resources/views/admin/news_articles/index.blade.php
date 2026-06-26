@extends('layouts.admin')

@section('title', 'News articles | Oakter Admin')

@section('content')
  <div class="admin-topbar">
    <div>
      <h1>News articles</h1>
      <p>Manage press coverage shown on the Media Insights page.</p>
    </div>
    <a class="admin-link-button" href="{{ route('admin.news-articles.create') }}">Add article</a>
  </div>

  <section class="admin-panel">
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th class="admin-table-image-cell">Image</th>
            <th>Title</th>
            <th>Source</th>
            <th>Published</th>
            <th>Status</th>
            <th class="admin-table-actions-cell">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($articles as $article)
            <tr>
              <td class="admin-table-image-cell">
                @if ($article->imageUrl())
                  <img
                    class="admin-news-thumb"
                    src="{{ $article->imageUrl() }}"
                    alt=""
                    width="48"
                    height="36"
                    loading="lazy"
                  />
                @else
                  <span class="admin-muted">—</span>
                @endif
              </td>
              <td class="admin-product-list-cell">
                <strong>{{ $article->title }}</strong>
                @if ($article->url)
                  <span class="admin-muted">{{ $article->url }}</span>
                @endif
              </td>
              <td>{{ $article->source ?? '—' }}</td>
              <td>{{ $article->formattedPublishedDate() }}</td>
              <td>
                @if ($article->is_published)
                  <span class="admin-badge is-paid">Published</span>
                @else
                  <span class="admin-badge is-pending">Draft</span>
                @endif
              </td>
              <td class="admin-table-actions-cell">
                <div class="admin-table-actions">
                  <a class="admin-link-button secondary" href="{{ route('admin.news-articles.edit', $article) }}">Edit</a>
                  <form method="post" action="{{ route('admin.news-articles.destroy', $article) }}" onsubmit="return confirm('Delete this article?');">
                    @csrf
                    @method('DELETE')
                    <button class="admin-link-button secondary" type="submit">Delete</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr class="admin-table-empty">
              <td colspan="6">No news articles yet.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>
@endsection
