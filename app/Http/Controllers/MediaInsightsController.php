<?php

namespace App\Http\Controllers;

use App\Models\NewsArticle;
use Illuminate\View\View;

class MediaInsightsController extends Controller
{
    public function __invoke(): View
    {
        $articles = NewsArticle::query()
            ->published()
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->get();

        return view('website.media_insights', compact('articles'));
    }
}
