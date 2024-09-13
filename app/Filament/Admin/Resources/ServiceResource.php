<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ServiceResource\Pages;
use App\Filament\Admin\Resources\ServiceResource\Pages\CreateService;
use App\Forms\Components\Select;
use App\Forms\Components\TableRepeater;
use App\Models\Service;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static bool $shouldRegisterNavigation = false;

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
                Section::make()
                    ->maxWidth('3xl')
                    ->schema([
                        TextInput::make('name')
                            ->label(__('service.field.name'))
                            ->columnSpanFull()
                            ->required(),

                        Select::make('counseling_sheet')
                            ->label(__('nomenclature.labels.counseling_sheet')),

                    ]),
                TableRepeater::make('serviceInterventions')
                    ->relationship('serviceInterventions')
                    ->label(__('nomenclature.headings.service_intervention'))
                    ->columnSpanFull()
                    ->hideLabels()
                    ->addActionLabel(__('nomenclature.actions.add_intervention'))
                    ->schema([
                        Placeholder::make('id')
                            ->label(__('nomenclature.labels.nr'))
                            ->content(function () {
                                static $index = 1;

                                return $index++;
                            })
                            ->hiddenLabel(),
                        TextInput::make('name')
                            ->label(__('nomenclature.labels.intervention_name')),
                        Toggle::make('status'),
                    ])
                    ->deleteAction(
                        fn (Action $action) => $action
                            ->disabled(function (array $arguments, TableRepeater $component): bool {
                                $items = $component->getState();
                                $currentItem = $items[$arguments['item']];
                                // TODO disable if intervention is used

                                return $currentItem['status'];
                            })
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

            ])
            ->actions([

            ]);
    }

    public static function getPages(): array
    {
        return [
            // TODO remove index and page
            'index' => Pages\ManageServices::route('/'),
            'create' => CreateService::route('/create'),
            'view' => Pages\ViewService::route('/{record}'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
