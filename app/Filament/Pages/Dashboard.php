<?php

namespace App\Filament\Pages;

use App\Enums\PostStatus;
use App\Models\Post;
use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $title = 'Dashboard';

    protected static string $view = 'filament.pages.dashboard';

    protected function getViewData(): array
    {
        return [
            'totalPosts' => Post::query()->count(),
            'publishedPosts' => Post::query()->where('status', PostStatus::Published)->count(),
        ];
    }
}
