<?php

namespace App\Http\Controllers;

use App\Enums\PostStatus;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
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
        $tagSlug = $request->query('tag');
        $page = (int) $request->query('page', 1);

        $posts = $this->cacheService->getPublishedPosts(
            page: $page,
            perPage: 6,
            search: $search ?: null,
            tagSlug: $tagSlug
        );

        // Preserve query string for pagination
        $posts->withQueryString();

        // Get primary author (first user or most active author)
        $primaryAuthor = User::query()->first();

        // Get popular tags for sidebar (cached)
        $popularTags = $this->cacheService->getPopularTags();

        return view('blog.index', [
            'posts' => $posts,
            'search' => $search,
            'primaryAuthor' => $primaryAuthor,
            'popularTags' => $popularTags,
            'currentTag' => $tagSlug,
        ]);
    }

    public function show(Post $post)
    {
        $post->loadMissing(['author', 'tags']);

        if ($post->status !== PostStatus::Published || optional($post->published_at)->isFuture()) {
            abort(404);
        }

        $bodyHtml = Str::markdown($post->body ?? '', [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        $comments = Comment::query()
            ->where('post_id', $post->id)
            ->where('status', 'approved')
            ->latest()
            ->get();

        // Get related posts
        $relatedPosts = $post->getRelatedPosts(3);

        return view('blog.show', [
            'post' => $post,
            'bodyHtml' => $bodyHtml,
            'comments' => $comments,
            'relatedPosts' => $relatedPosts,
        ]);
    }
}
