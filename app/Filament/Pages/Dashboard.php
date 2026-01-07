<?php

namespace App\Filament\Pages;

use App\Enums\CommentStatus;
use App\Enums\PostStatus;
use App\Models\Comment;
use App\Models\Post;
use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $title = 'Dashboard';

    protected static string $view = 'filament.pages.dashboard';

    protected function getViewData(): array
    {
        $recentComments = Comment::query()
            ->with('post')
            ->latest()
            ->limit(6)
            ->get();

        return [
            'totalPosts' => Post::query()->count(),
            'publishedPosts' => Post::query()->where('status', PostStatus::Published)->count(),
            'pendingComments' => Comment::query()->where('status', CommentStatus::Pending)->count(),
            'totalComments' => Comment::query()->count(),
            'recentComments' => $recentComments,
        ];
    }
}
