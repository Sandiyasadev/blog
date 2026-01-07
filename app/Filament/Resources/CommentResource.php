<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommentResource\Pages;
use App\Enums\CommentStatus;
use App\Models\Comment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Moderation';

    protected static ?string $recordTitleAttribute = 'author_name';

    public static function getNavigationLabel(): string
    {
        return 'Comments';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Comment')
                    ->schema([
                        Forms\Components\Select::make('post_id')
                            ->relationship('post', 'title')
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('author_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('author_email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('author_url')
                            ->url()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('body_raw')
                            ->label('Body (raw)')
                            ->rows(5)
                            ->columnSpanFull()
                            ->disabled(),
                        Forms\Components\Textarea::make('body_sanitized')
                            ->label('Body (sanitized)')
                            ->rows(5)
                            ->columnSpanFull()
                            ->disabled(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Moderation')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'spam' => 'Spam',
                                'deleted' => 'Deleted',
                            ])
                            ->required(),
                        Forms\Components\DateTimePicker::make('approved_at')
                            ->label('Approved at'),
                        Forms\Components\TextInput::make('ip_address')
                            ->disabled(),
                        Forms\Components\TextInput::make('user_agent')
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('post.title')
                    ->label('Post')
                    ->limit(30)
                    ->searchable(),
                Tables\Columns\TextColumn::make('author_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('body_sanitized')
                    ->label('Comment')
                    ->limit(60),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'spam',
                        'gray' => 'deleted',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'spam' => 'Spam',
                        'deleted' => 'Deleted',
                    ]),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Comment $record) => $record->status !== CommentStatus::Approved)
                    ->action(fn (Comment $record) => $record->update([
                        'status' => CommentStatus::Approved,
                        'approved_at' => Carbon::now(),
                    ])),
                Tables\Actions\Action::make('spam')
                    ->label('Spam')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->visible(fn (Comment $record) => $record->status !== CommentStatus::Spam)
                    ->action(fn (Comment $record) => $record->update([
                        'status' => CommentStatus::Spam,
                        'approved_at' => null,
                    ])),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
                        ->label('Approve selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each(fn (Comment $record) => $record->update([
                            'status' => CommentStatus::Approved,
                            'approved_at' => Carbon::now(),
                        ]))),
                    Tables\Actions\BulkAction::make('spam')
                        ->label('Mark as spam')
                        ->icon('heroicon-o-no-symbol')
                        ->color('danger')
                        ->action(fn ($records) => $records->each(fn (Comment $record) => $record->update([
                            'status' => CommentStatus::Spam,
                            'approved_at' => null,
                        ]))),
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Comment::query()->where('status', CommentStatus::Pending)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComments::route('/'),
            'create' => Pages\CreateComment::route('/create'),
            'edit' => Pages\EditComment::route('/{record}/edit'),
        ];
    }
}
