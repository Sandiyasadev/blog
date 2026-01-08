<?php

namespace App\Models;

use App\Enums\PostStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Post extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'excerpt',
        'body',
        'body_rendered',
        'status',
        'published_at',
        'cover_image_path',
    ];

    protected $casts = [
        'status' => PostStatus::class,
        'published_at' => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', PostStatus::Published)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Get estimated reading time in minutes.
     */
    public function getReadingTimeAttribute(): int
    {
        $wordCount = str_word_count(strip_tags($this->body ?? ''));
        $minutes = (int) ceil($wordCount / 200); // Average reading speed: 200 words/minute

        return max(1, $minutes);
    }

    /**
     * Get reading time formatted string.
     */
    public function getReadingTimeTextAttribute(): string
    {
        $minutes = $this->reading_time;

        return $minutes . ' min read';
    }

    /**
     * Get related posts based on recency.
     */
    public function getRelatedPosts(int $limit = 3): \Illuminate\Database\Eloquent\Collection
    {
        return self::query()
            ->published()
            ->where('id', '!=', $this->id)
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }
}
