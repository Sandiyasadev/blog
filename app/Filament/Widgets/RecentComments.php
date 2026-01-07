<?php

namespace App\Filament\Widgets;

use App\Models\Comment;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentComments extends BaseWidget
{
    protected static ?string $heading = 'Recent Comments';

    protected int | string | array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return Comment::query()
            ->with('post')
            ->latest();
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('post.title')
                ->label('Post')
                ->limit(30)
                ->searchable(),
            Tables\Columns\TextColumn::make('author_name')
                ->label('User')
                ->searchable(),
            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->label('Date'),
            Tables\Columns\TextColumn::make('status')
                ->badge()
                ->colors([
                    'warning' => 'pending',
                    'success' => 'approved',
                    'danger' => 'spam',
                    'gray' => 'deleted',
                ]),
        ];
    }

    public function getDefaultTableRecordsPerPageSelectOption(): int
    {
        return 5;
    }
}
