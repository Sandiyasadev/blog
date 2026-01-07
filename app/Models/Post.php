<?php

namespace App\Models;

use App\Enums\PostStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'allow_comments',
    ];

    protected $casts = [
        'status' => PostStatus::class,
        'published_at' => 'datetime',
        'allow_comments' => 'boolean',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', PostStatus::Published)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope to filter by tag slug.
     */
    public function scopeWithTag(Builder $query, string $tagSlug): Builder
    {
        return $query->whereHas('tags', function ($q) use ($tagSlug) {
            $q->where('slug', $tagSlug);
        });
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
     * Get related posts based on shared tags.
     */
    public function getRelatedPosts(int $limit = 3): \Illuminate\Database\Eloquent\Collection
    {
        $tagIds = $this->tags->pluck('id');

        if ($tagIds->isEmpty()) {
            // If no tags, get recent posts
            return self::query()
                ->published()
                ->where('id', '!=', $this->id)
                ->latest('published_at')
                ->limit($limit)
                ->get();
        }

        return self::query()
            ->published()
            ->where('id', '!=', $this->id)
            ->whereHas('tags', function ($query) use ($tagIds) {
                $query->whereIn('tags.id', $tagIds);
            })
            ->withCount(['tags' => function ($query) use ($tagIds) {
                $query->whereIn('tags.id', $tagIds);
            }])
            ->orderByDesc('tags_count')
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }
}
