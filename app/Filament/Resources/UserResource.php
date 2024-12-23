<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\Resources\UserResource\RelationManagers\PhoneNumbersRelationManager;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rules\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $modelLabel = 'Usuário';
    protected static ?string $pluralModelLabel = 'Usuários';
    protected static ?string $navigationLabel = 'Usuários';
    protected static ?string $navigationGroup = 'Usuários';
    protected static ?int $navigationSort = 1;


    public static function form(Form $form): Form
    {
        return $form
            ->columns(null)
            ->schema([
                Tabs::make()
                    ->columns(2)
                    ->tabs([
                        Tabs\Tab::make('User Information')
                            ->icon('heroicon-o-user')
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255),
                                // Forms\Components\DateTimePicker::make('email_verified_at'),
                                TextInput::make('password')
                                    ->password()
                                    ->required()
                                    ->revealable(filament()->arePasswordsRevealable())
                                    ->rule(Password::default())
                                    ->autocomplete(false)
                                    ->dehydrated(fn($state): bool => filled($state))
                                    ->live(debounce: 500)
                                    ->same('password Confirmation')
                                    ->maxLength(255),
                                TextInput::make('passwordConfirmation')
                                    ->password()
                                    ->revealable(filament()->arePasswordsRevealable())
                                    ->required()
                                    ->visible(fn(Get $get): bool => filled($get('password')))
                                    ->dehydrated(false),
                            ]),
                        Tabs\Tab::make('Phofe Number')
                            ->columns(null)
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Repeater::make('phoneNumbers')
                                    ->relationship()
                                    ->columns(4)
                                    ->schema([
                                        TextInput::make('type')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('ddi')
                                            ->mask('99')
                                            ->prefix('+')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('ddd')
                                            ->prefix('0')
                                            ->mask('99')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('number')
                                            ->mask('99999-9999')
                                            ->required()
                                            ->maxLength(255),
                                    ])
                            ]),
                    ]),
            ]);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('phoneNumbers.full_number')
                    ->label('Phone Number')
                    ->searchable()
                    ->sortable()
                    ->listWithLineBreaks(),
                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),

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
            PhoneNumbersRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'view' => Pages\ViewUser::route('/{record}/view'),
        ];
    }
}
