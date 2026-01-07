<?php

namespace App\Observers;

use App\Models\Post;
use App\Services\PostCacheService;

class PostObserver
{
    public function __construct(
        private readonly PostCacheService $cacheService
    ) {}

    public function created(Post $post): void
    {
        $this->cacheService->clearCache();
    }

    public function updated(Post $post): void
    {
        $this->cacheService->clearCache();
    }

    public function deleted(Post $post): void
    {
        $this->cacheService->clearCache();
    }

    public function restored(Post $post): void
    {
        $this->cacheService->clearCache();
    }

    public function forceDeleted(Post $post): void
    {
        $this->cacheService->clearCache();
    }
}
