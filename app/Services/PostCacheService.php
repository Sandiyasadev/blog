<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class PostCacheService
{
    private const CACHE_TTL = 3600; // 1 hour

    /**
     * Get published posts with caching.
     */
    public function getPublishedPosts(
        int $page = 1,
        int $perPage = 6,
        ?string $search = null,
        ?string $tagSlug = null
    ): LengthAwarePaginator {
        // Don't cache when searching - too many variations
        if ($search) {
            return $this->queryPosts($page, $perPage, $search, $tagSlug);
        }

        $cacheKey = sprintf('posts:page:%d:tag:%s', $page, $tagSlug ?? 'all');

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($page, $perPage, $tagSlug) {
            return $this->queryPosts($page, $perPage, null, $tagSlug);
        });
    }

    /**
     * Get popular tags with caching.
     */
    public function getPopularTags(int $limit = 10): Collection
    {
        return Cache::remember('tags:popular', self::CACHE_TTL, function () use ($limit) {
            return Tag::query()
                ->withCount('posts')
                ->orderByDesc('posts_count')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Clear all post-related caches.
     */
    public function clearCache(): void
    {
        // Clear all post pages cache
        Cache::forget('tags:popular');

        // Clear paginated caches (first 10 pages should be enough)
        for ($i = 1; $i <= 10; $i++) {
            Cache::forget(sprintf('posts:page:%d:tag:all', $i));
        }

        // Clear tag-filtered caches
        $tags = Tag::pluck('slug');
        foreach ($tags as $slug) {
            for ($i = 1; $i <= 5; $i++) {
                Cache::forget(sprintf('posts:page:%d:tag:%s', $i, $slug));
            }
        }
    }

    private function queryPosts(
        int $page,
        int $perPage,
        ?string $search,
        ?string $tagSlug
    ): LengthAwarePaginator {
        return Post::query()
            ->with(['author', 'tags'])
            ->published()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('excerpt', 'like', "%{$search}%");
                });
            })
            ->when($tagSlug, function ($query) use ($tagSlug) {
                $query->withTag($tagSlug);
            })
            ->latest('published_at')
            ->paginate($perPage, ['*'], 'page', $page);
    }
}
