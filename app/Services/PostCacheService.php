<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;
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
        ?string $search = null
    ): LengthAwarePaginator {
        // Don't cache when searching - too many variations
        if ($search) {
            return $this->queryPosts($page, $perPage, $search);
        }

        $cacheKey = sprintf('posts:page:%d', $page);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($page, $perPage) {
            return $this->queryPosts($page, $perPage, null);
        });
    }

    /**
     * Clear all post-related caches.
     */
    public function clearCache(): void
    {
        // Clear paginated caches (first 10 pages should be enough)
        for ($i = 1; $i <= 10; $i++) {
            Cache::forget(sprintf('posts:page:%d', $i));
        }
    }

    private function queryPosts(
        int $page,
        int $perPage,
        ?string $search
    ): LengthAwarePaginator {
        return Post::query()
            ->with(['author'])
            ->published()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('excerpt', 'like', "%{$search}%");
                });
            })
            ->latest('published_at')
            ->paginate($perPage, ['*'], 'page', $page);
    }
}
