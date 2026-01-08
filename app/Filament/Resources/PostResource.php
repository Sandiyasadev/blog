<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Content';

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationLabel(): string
    {
        return 'Posts';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Editor')
                            ->description('Write the story. Markdown is supported.')
                            ->icon('heroicon-o-pencil-square')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Enter your post title...')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $state, Set $set) {
                                        $set('slug', Str::slug($state));
                                    })
                                    ->columnSpanFull(),
                                Forms\Components\Textarea::make('excerpt')
                                    ->rows(3)
                                    ->placeholder('Short summary for list pages and SEO...')
                                    ->columnSpanFull(),
                                Forms\Components\MarkdownEditor::make('body')
                                    ->required()
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'heading',
                                        'strike',
                                        'blockquote',
                                        'link',
                                        'bulletList',
                                        'orderedList',
                                        'codeBlock',
                                    ])
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpan(2),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Post Settings')
                            ->description('Publishing, visibility, and media.')
                            ->icon('heroicon-o-adjustments-horizontal')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'published' => 'Published',
                                    ])
                                    ->live()
                                    ->afterStateUpdated(function (string $state, Set $set) {
                                        if ($state === 'published') {
                                            $set('published_date', now()->toDateString());
                                            $set('published_time', now()->format('H:i'));
                                        }

                                        if ($state === 'draft') {
                                            $set('published_date', null);
                                            $set('published_time', null);
                                        }
                                    })
                                    ->required(),
                                Forms\Components\DatePicker::make('published_date')
                                    ->label('Publish date')
                                    ->native(true)
                                    ->visible(fn (Get $get) => $get('status') === 'published')
                                    ->helperText('For Published, this is auto-set.')
                                    ->afterStateHydrated(function (Set $set, $record) {
                                        if ($record?->published_at) {
                                            $set('published_date', $record->published_at->toDateString());
                                        }
                                    }),
                                Forms\Components\TimePicker::make('published_time')
                                    ->label('Publish time')
                                    ->seconds(false)
                                    ->minutesStep(5)
                                    ->visible(fn (Get $get) => $get('status') === 'published')
                                    ->afterStateHydrated(function (Set $set, $record) {
                                        if ($record?->published_at) {
                                            $set('published_time', $record->published_at->format('H:i'));
                                        }
                                    }),
                                Forms\Components\Select::make('user_id')
                                    ->label('Author')
                                    ->relationship('author', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->default(fn () => auth()->id())
                                    ->disabled(fn () => User::query()->count() === 1)
                                    ->dehydrated()
                                    ->required(),
                                Forms\Components\TextInput::make('slug')
                                    ->prefix('/posts/')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                                Forms\Components\FileUpload::make('cover_image_path')
                                    ->label('Featured Image')
                                    ->disk('public')
                                    ->directory('posts')
                                    ->image()
                                    ->imageEditor()
                                    ->imagePreviewHeight('200')
                                    ->helperText('Recommended: 1600×900px, max 2MB.')
                                    ->maxSize(2048),
                            ]),
                    ])
                    ->columnSpan(1),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('author.name')
                    ->label('Author')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'draft',
                        'success' => 'published',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ]),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
        $count = Post::query()->where('status', PostStatus::Draft)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
