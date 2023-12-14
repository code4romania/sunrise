<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources;

use App\Filament\Organizations\Resources\ServiceResource\Pages;
use App\Models\Service;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.configurations._group');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.configurations.services');
    }

    public static function getModelLabel(): string
    {
        return __('service.label.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('service.label.plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('service.field.name'))
                    ->searchable(),

                TextColumn::make('interventions_count')
                    ->counts('interventions'),

            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (Service $record) => ! $record->interventions_count),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageServices::route('/'),
        ];
    }
}
