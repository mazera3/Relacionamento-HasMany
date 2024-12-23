<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $modelLabel = 'Post';
    protected static ?string $pluralModelLabel = 'Postagens';
    protected static ?string $navigationLabel = 'Postagens';
    protected static ?string $navigationGroup = 'Posts';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (?string $state, Get $get, Set $set) {
                            if (blank($get('slug'))) {
                                $set('slug', Str::slug($get('title')));
                            }
                        }),
                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique('posts', 'slug', ignoreRecord: true),
                    Forms\Components\Textarea::make('content')
                        ->columnSpanFull(),
                    Select::make('categories')
                        ->multiple()
                        ->preload()
                        ->relationship('categories', 'name')
                        ->createOptionForm([
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\RichEditor::make('content'),
                            Forms\Components\Toggle::make('published')
                                ->default(true),
                        ]),
                    Forms\Components\TextInput::make('tags'),
                    // ->separator(','),
                ])
                    ->columnSpan(2),
                Section::make([
                    Forms\Components\DateTimePicker::make('created_at')
                        ->disabled()
                        ->hiddenOn('create'),
                    Forms\Components\DateTimePicker::make('updated_at')
                        ->disabled()
                        ->hiddenOn('create'),
                    Forms\Components\Select::make('author_id')
                        ->relationship('author', 'name')
                        ->disabled()
                        ->default(auth()->id()),
                    Fieldset::make('Status')
                        ->schema([
                            Forms\Components\Toggle::make('published')
                                ->default(true),
                            Forms\Components\DateTimePicker::make('published_at')
                                ->default(now()),
                        ])->columns(1),
                    FileUpload::make('image')
                        ->previewable()
                ])
                    ->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('author_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\IconColumn::make('published')
                    ->boolean(),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
