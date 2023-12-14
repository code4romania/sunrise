<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources;

use App\Filament\Organizations\Resources\InterventionResource\Pages;
use App\Models\Intervention;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class InterventionResource extends Resource
{
    protected static ?string $model = Intervention::class;

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
        return __('intervention.label.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('intervention.label.plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                TextInput::make('name')
                    ->label(__('intervention.field.name'))
                    ->required(),

                Select::make('service_id')
                    ->label(__('service.field.name'))
                    ->relationship('service', 'name')
                    ->required(),

                Textarea::make('description')
                    ->label(__('intervention.field.description'))
                    ->placeholder(__('placeholder.service_description'))
                    ->rows(5)
                    ->columnSpanFull()
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('intervention.field.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('use_count')
                    ->label(__('intervention.field.use_count'))
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (Intervention $record) => true),
            ])
            ->groups([
                Group::make('service.name')
                    ->label(__('service.label.singular'))
                    ->collapsible(),
            ])
            ->defaultGroup('service.name');
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
            'index' => Pages\ManageInterventions::route('/'),

        ];
    }
}
