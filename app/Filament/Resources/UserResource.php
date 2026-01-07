<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return 'Authors';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Account Information')
                    ->description('Basic account details.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->maxLength(255)
                            ->helperText('Leave blank to keep current password.'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Profile Information')
                    ->description('Public author profile displayed on the blog.')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Title/Role')
                            ->placeholder('e.g., Editor in Chief, Writer, Contributor')
                            ->maxLength(100),
                        Forms\Components\Textarea::make('bio')
                            ->label('Biography')
                            ->placeholder('A short bio about yourself...')
                            ->rows(3)
                            ->maxLength(500),
                        Forms\Components\FileUpload::make('avatar_path')
                            ->label('Avatar')
                            ->disk('public')
                            ->directory('avatars')
                            ->image()
                            ->imageEditor()
                            ->circleCropper()
                            ->imagePreviewHeight('150')
                            ->helperText('Recommended: 200x200px square image.')
                            ->maxSize(1024),
                    ]),

                Forms\Components\Section::make('Social Links')
                    ->description('Your social media and website links.')
                    ->collapsed()
                    ->schema([
                        Forms\Components\TextInput::make('website_url')
                            ->label('Website URL')
                            ->url()
                            ->placeholder('https://yourwebsite.com')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('twitter_handle')
                            ->label('Twitter/X Handle')
                            ->prefix('@')
                            ->placeholder('username')
                            ->maxLength(50),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar_path')
                    ->label('Avatar')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => $record->avatar_url),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Role')
                    ->placeholder('Author')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('posts_count')
                    ->label('Posts')
                    ->counts('posts')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
