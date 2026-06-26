<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class NewsArticle extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'source',
        'url',
        'image_path',
        'published_at',
        'is_published',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_published' => 'boolean',
        ];
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function formattedPublishedDate(): string
    {
        return $this->published_at->format('F d, Y');
    }

    public function imageUrl(): ?string
    {
        if (! is_string($this->image_path) || $this->image_path === '') {
            return null;
        }

        if (! Storage::disk('public')->exists($this->image_path)) {
            return null;
        }

        return asset('storage/'.$this->image_path);
    }
}
