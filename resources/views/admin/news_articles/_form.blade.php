<div class="admin-field">
  <label for="title">Title</label>
  <input id="title" type="text" name="title" value="{{ old('title', $article->title) }}" required />
  @error('title')<p class="admin-error">{{ $message }}</p>@enderror
</div>

<div class="admin-field">
  <label for="source">Source (optional)</label>
  <input id="source" type="text" name="source" value="{{ old('source', $article->source) }}" placeholder="e.g. India Today" />
  @error('source')<p class="admin-error">{{ $message }}</p>@enderror
</div>

<div class="admin-field">
  <label for="url">Article URL (optional)</label>
  <input id="url" type="url" name="url" value="{{ old('url', $article->url) }}" placeholder="https://..." />
  @error('url')<p class="admin-error">{{ $message }}</p>@enderror
</div>

<div class="admin-field">
  <label for="published_at">Published date</label>
  <input
    id="published_at"
    type="datetime-local"
    name="published_at"
    value="{{ old('published_at', optional($article->published_at)->format('Y-m-d\TH:i')) }}"
    required
  />
  @error('published_at')<p class="admin-error">{{ $message }}</p>@enderror
</div>

<div class="admin-field">
  <label class="admin-checkbox">
    <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $article->is_published)) />
    <span>Published on website</span>
  </label>
</div>

<div class="admin-field">
  <label for="image">Image @unless ($article->exists)<span aria-hidden="true">*</span>@endunless</label>
  @if ($article->imageUrl())
    <img class="admin-news-preview" src="{{ $article->imageUrl() }}" alt="Current article image" />
    <p class="admin-muted">Upload a new image to replace the current one.</p>
  @endif
  <input id="image" type="file" name="image" accept="image/jpeg,image/png,image/webp" @unless ($article->exists) required @endunless />
  @error('image')<p class="admin-error">{{ $message }}</p>@enderror
</div>
