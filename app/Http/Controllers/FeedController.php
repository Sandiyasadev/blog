<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class FeedController extends Controller
{
    public function rss(): Response
    {
        $posts = Post::query()
            ->with('author')
            ->published()
            ->latest('published_at')
            ->limit(20)
            ->get();

        $content = view('feed.rss', [
            'posts' => $posts,
        ])->render();

        return response($content, 200, [
            'Content-Type' => 'application/rss+xml; charset=utf-8',
        ]);
    }

    public function atom(): Response
    {
        $posts = Post::query()
            ->with('author')
            ->published()
            ->latest('published_at')
            ->limit(20)
            ->get();

        $content = view('feed.atom', [
            'posts' => $posts,
        ])->render();

        return response($content, 200, [
            'Content-Type' => 'application/atom+xml; charset=utf-8',
        ]);
    }
}
