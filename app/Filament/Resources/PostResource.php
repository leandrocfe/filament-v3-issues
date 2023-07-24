<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-m-document-text';

    protected static ?string $navigationGroup = 'Posts';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Card::make()
                    ->schema([

                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (?string $state, Get $get, Set $set) {
                                if (blank($get('slug'))) {
                                    $set('slug', Str::slug($get('title')));
                                }
                            }),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(table: 'posts', column: 'slug', ignoreRecord: true),

                        Forms\Components\RichEditor::make('content')
                            ->columnSpanFull(),

                        Forms\Components\Select::make('categories')
                            ->multiple()
                            ->preload()
                            ->relationship('categories', 'name')
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\RichEditor::make('content')
                                    ->columnSpanFull()
                            ]),

                        Forms\Components\TagsInput::make('tags')->separator(','),
                    ])
                    ->columnSpan(2),
                Card::make()
                    ->schema([

                        Forms\Components\DateTimePicker::make('created_at')
                            ->disabled()
                            ->hiddenOn('create'),

                        Forms\Components\DateTimePicker::make('updated_at')
                            ->disabled()
                            ->hiddenOn('create'),

                        Forms\Components\Select::make('author_id')
                            ->relationship('author', 'name')
                            ->disabled()
                            ->default(auth()->id())
                            ->dehydrated(false),

                        Forms\Components\Fieldset::make('Status')
                            ->schema([
                                Forms\Components\Toggle::make('published')
                                    ->default(true),
                                Forms\Components\DateTimePicker::make('published_at')
                                    ->default(now()),
                            ])->columns(1),

                    ])
                    ->columnSpan(1),

            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),

                Tables\Columns\IconColumn::make('published')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
