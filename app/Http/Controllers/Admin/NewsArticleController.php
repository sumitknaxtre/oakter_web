<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreNewsArticleRequest;
use App\Http\Requests\Admin\UpdateNewsArticleRequest;
use App\Models\NewsArticle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class NewsArticleController extends Controller
{
    public function index(): View
    {
        $articles = NewsArticle::query()
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->get();

        return view('admin.news_articles.index', compact('articles'));
    }

    public function create(): View
    {
        return view('admin.news_articles.create', [
            'article' => new NewsArticle([
                'is_published' => true,
                'published_at' => now(),
            ]),
        ]);
    }

    public function store(StoreNewsArticleRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        NewsArticle::query()->create([
            'title' => $validated['title'],
            'source' => $validated['source'] ?? null,
            'url' => $validated['url'] ?? null,
            'image_path' => $this->storeImage($request->file('image')),
            'published_at' => $validated['published_at'],
            'is_published' => $validated['is_published'] ?? false,
        ]);

        return redirect()
            ->route('admin.news-articles.index')
            ->with('status', 'News article created successfully.');
    }

    public function edit(NewsArticle $newsArticle): View
    {
        return view('admin.news_articles.edit', [
            'article' => $newsArticle,
        ]);
    }

    public function update(UpdateNewsArticleRequest $request, NewsArticle $newsArticle): RedirectResponse
    {
        $validated = $request->validated();

        $attributes = [
            'title' => $validated['title'],
            'source' => $validated['source'] ?? null,
            'url' => $validated['url'] ?? null,
            'published_at' => $validated['published_at'],
            'is_published' => $validated['is_published'] ?? false,
        ];

        if ($request->hasFile('image')) {
            $this->deleteImage($newsArticle->image_path);
            $attributes['image_path'] = $this->storeImage($request->file('image'));
        }

        $newsArticle->update($attributes);

        return redirect()
            ->route('admin.news-articles.index')
            ->with('status', 'News article updated successfully.');
    }

    public function destroy(NewsArticle $newsArticle): RedirectResponse
    {
        $this->deleteImage($newsArticle->image_path);
        $newsArticle->delete();

        return redirect()
            ->route('admin.news-articles.index')
            ->with('status', 'News article deleted successfully.');
    }

    private function storeImage(UploadedFile $file): string
    {
        return $file->store('news', 'public');
    }

    private function deleteImage(?string $path): void
    {
        if (! is_string($path) || $path === '') {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}
