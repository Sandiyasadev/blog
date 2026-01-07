<?php

namespace App\Filament\Widgets;

use App\Enums\CommentStatus;
use App\Enums\PostStatus;
use App\Models\Comment;
use App\Models\Post;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BlogStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Posts', Post::query()->count())
                ->description('All published & drafts')
                ->descriptionIcon('heroicon-o-document-text')
                ->color('primary'),
            Stat::make('Published Posts', Post::query()->where('status', PostStatus::Published)->count())
                ->description('Live on site')
                ->descriptionIcon('heroicon-o-globe-alt')
                ->color('success'),
            Stat::make('Pending Comments', Comment::query()->where('status', CommentStatus::Pending)->count())
                ->description('Needs review')
                ->descriptionIcon('heroicon-o-chat-bubble-left-right')
                ->color('warning'),
        ];
    }
}
