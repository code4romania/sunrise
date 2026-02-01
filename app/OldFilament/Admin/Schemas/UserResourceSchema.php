<?php

declare(strict_types=1);

namespace App\Filament\Admin\Schemas;

use App\Forms\Components\Select;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UserResourceSchema
{
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->inlineLabel()
            ->components(self::getFormComponents());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(self::getTableColumns())
            ->filters(self::getTableFilters())
            ->recordActions(self::getTableActions())
            ->toolbarActions(self::getToolbarActions());
    }

    public static function getFormComponents(): array
    {
        return [
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
                        ->visible(fn (\Filament\Schemas\Components\Utilities\Get $get) => \boolval($get('is_admin')) === false)
                        ->multiple()
                        ->preload()
                        ->required(),
                ]),
        ];
    }

    public static function getTableColumns(): array
    {
        return [
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
        ];
    }

    public static function getTableFilters(): array
    {
        return [
            TernaryFilter::make('is_admin')
                ->label(__('field.role'))
                ->trueLabel(__('user.role.admin'))
                ->falseLabel(__('user.role.user')),

            SelectFilter::make('organizations')
                ->relationship('organizations', 'name')
                ->multiple(),
        ];
    }

    public static function getTableActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    public static function getToolbarActions(): array
    {
        return [
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ];
    }
}
