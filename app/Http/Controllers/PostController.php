<?php

namespace App\Http\Controllers;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Services\PostCacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function __construct(
        private readonly PostCacheService $cacheService
    ) {}

    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $page = (int) $request->query('page', 1);

        $posts = $this->cacheService->getPublishedPosts(
            page: $page,
            perPage: 6,
            search: $search ?: null
        );

        // Preserve query string for pagination
        $posts->withQueryString();

        return view('blog.index', [
            'posts' => $posts,
            'search' => $search,
        ]);
    }

    public function show(Post $post)
    {
        $post->loadMissing(['author']);

        if ($post->status !== PostStatus::Published || optional($post->published_at)->isFuture()) {
            abort(404);
        }

        $bodyHtml = Str::markdown($post->body ?? '', [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        // Get related posts
        $relatedPosts = $post->getRelatedPosts(3);

        return view('blog.show', [
            'post' => $post,
            'bodyHtml' => $bodyHtml,
            'relatedPosts' => $relatedPosts,
        ]);
    }
}
