<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\MonitoringResource\Pages;

use App\Concerns\HasParentResource;
use App\Filament\Organizations\Resources\MonitoringResource;
use App\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;

class CreateMonitoring extends CreateRecord
{
    use HasParentResource;

    protected static string $resource = MonitoringResource::class;

    public function form(Form $form): Form
    {
        return $form->schema([
            Wizard::make()
                ->columnSpanFull()
                ->steps([
                    Wizard\Step::make('aaaa')
                        ->schema([
                            Grid::make()
                                ->maxWidth('3xl')
                                ->schema([
                                    DatePicker::make('date'),
                                    TextInput::make('number'),
                                    DatePicker::make('start_date'),
                                    DatePicker::make('end_date'),
                                    Select::make('team'),
                                ]),
                        ]),
                    Wizard\Step::make('bbbb')
                        ->schema(function () {
                            $fields = [];
                            foreach ($this->parent->children as $key => $child) {
                                $fields[] = Group::make()
                                    ->maxWidth('3xl')
                                    ->schema([
                                        TextInput::make('children.' . $key . '.name')
                                            ->columnSpanFull()
                                            ->default($child['name']),
                                        Grid::make()
                                            ->schema([
                                                TextInput::make('children.' . $key . '.status')
                                                    ->default($child['status']),
                                                TextInput::make('children.' . $key . '.age')
                                                    ->default($child['age']),

                                                TextInput::make('children.' . $key . '.birth_date')
                                                    ->default($child['birthdate']),
                                                TextInput::make('children.' . $key . '.aggressor_relationship'),
                                                TextInput::make('children.' . $key . '.maintenance_sources'),
                                                TextInput::make('children.' . $key . '.location'),
                                                Textarea::make('children.' . $key . '.observations')
                                                    ->columnSpanFull(),
                                            ]),
                                    ]);
                            }

                            return $fields;
                        }),
                    Wizard\Step::make('cccc')
                        ->schema([
                            Group::make()
                                ->maxWidth('3xl')
                                ->schema([
                                    Grid::make()
                                        ->schema([
                                            DatePicker::make('admittance_date'),
                                            TextInput::make('admittance_disposition'),
                                        ]),

                                    Textarea::make('services_in_center'),

                                    ...$this->getGeneralMonitoringDataFields(),

                                    TextInput::make('progress'),
                                    TextInput::make('observation'),

                                ]),
                        ]),

                ]),
        ]);
    }

    private function getGeneralMonitoringDataFields(): array
    {
        $formFields = [];
        $fields = [
            'protection_measures',
            'health_measures',
            'legal_measures',
            'psychological_measures',
            'aggressor_relationship',
            'others',
        ];

        foreach ($fields as $field) {
            $formFields[] = Placeholder::make($field);
//                    ->content($field);
            $formFields[] = Textarea::make($field . '.objection');
            $formFields[] = Textarea::make($field . '.activity');
            $formFields[] = Textarea::make($field . '.conclusion');
        }

        return $formFields;
    }
}
