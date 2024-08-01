<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Forms\Components\Select;
use App\Models\User;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $tenantOwnershipRelationshipName = 'organizations';

    protected static ?int $navigationSort = 31;

    public static function getModelLabel(): string
    {
        return __('user.label.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('user.label.plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->inlineLabel()
            ->schema([
                Section::make()
                    ->maxWidth('3xl')
                    ->schema([
                        TextInput::make('first_name')
                            ->label(__('field.first_name'))
                            ->maxLength(100)
                            ->required(),

                        TextInput::make('last_name')
                            ->label(__('field.last_name'))
                            ->maxLength(100)
                            ->required(),

                        TextInput::make('email')
                            ->label(__('field.email'))
                            ->unique(ignoreRecord: true)
                            ->columnSpanFull()
                            ->maxLength(200)
                            ->email()
                            ->required(),
                    ]),

                Section::make()
                    ->maxWidth('3xl')
                    // ->columns()
                    ->schema([
                        Radio::make('is_admin')
                            ->label(__('field.role'))
                            ->inlineLabel()
                            ->boolean(
                                trueLabel: __('user.role.admin'),
                                falseLabel: __('user.role.user'),
                            )
                            ->default(false)
                            ->live(),

                        Select::make('organizations')
                            ->relationship('organizations', titleAttribute: 'name')
                            ->label(__('field.organizations'))
                            ->inlineLabel()
                            ->visible(fn (Get $get) => \boolval($get('is_admin')) === false)
                            ->multiple()
                            ->preload()
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('first_name')
                    ->searchable(),

                TextColumn::make('last_name')
                    ->searchable(),

                TextColumn::make('organizations.name')
                    ->wrap(),

                TextColumn::make('is_admin')
                    ->label(__('field.role')),

                TextColumn::make('account_status'),

                TextColumn::make('last_login_at')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_admin')
                    ->label(__('field.role'))
                    ->trueLabel(__('user.role.admin'))
                    ->falseLabel(__('user.role.user')),

                SelectFilter::make('organizations')
                    ->relationship('organizations', 'name')
                    ->multiple(),
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
