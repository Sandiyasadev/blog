<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $posts = Post::query()
            ->published()
            ->select(['slug', 'updated_at'])
            ->orderByDesc('published_at')
            ->get();

        $content = view('sitemap.index', [
            'posts' => $posts,
        ])->render();

        return response($content, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }
}
