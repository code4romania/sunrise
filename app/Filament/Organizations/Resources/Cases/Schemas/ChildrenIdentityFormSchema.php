<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Schemas;

use App\Enums\GenderShortValues;
use App\Forms\Components\DatePicker;
use App\Forms\Components\Repeater;
use App\Forms\Components\Select;
use Carbon\Carbon;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class ChildrenIdentityFormSchema
{
    /**
     * @return array<int, mixed>
     */
    public static function getSchema(): array
    {
        return [
            Grid::make()
                ->maxWidth('3xl')
                ->schema([
                    Checkbox::make('doesnt_have_children')
                        ->label(__('field.doesnt_have_children'))
                        ->live()
                        ->columnSpanFull()
                        ->afterStateUpdated(function (bool $state, Set $set, Get $get) {
                            $fields = [
                                'children_total_count',
                                'children_care_count',
                                'children_under_18_care_count',
                                'children_18_care_count',
                                'children_accompanying_count',
                                'children',
                                'children_notes',
                            ];
                            if ($state) {
                                $oldFields = [];
                                foreach ($fields as $field) {
                                    $oldFields[$field] = $get($field);
                                    $set($field, null);
                                }
                                $set('old_children_fields', $oldFields);

                                return;
                            }
                            $oldFields = $get('old_children_fields');
                            if (! $oldFields) {
                                return;
                            }
                            foreach ($oldFields as $field => $value) {
                                $set($field, $value);
                            }
                        }),

                    Hidden::make('old_children_fields'),

                    Grid::make()
                        ->disabled(fn (Get $get) => $get('doesnt_have_children'))
                        ->hidden(fn (Get $get) => $get('doesnt_have_children'))
                        ->columnSpanFull()
                        ->schema([
                            TextInput::make('children_total_count')
                                ->label(__('field.children_total_count'))
                                ->placeholder(__('placeholder.number'))
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(99),

                            TextInput::make('children_care_count')
                                ->label(__('field.children_care_count'))
                                ->placeholder(__('placeholder.number'))
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(99),

                            TextInput::make('children_under_18_care_count')
                                ->label(__('field.children_under_18_care_count'))
                                ->placeholder(__('placeholder.number'))
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(99),

                            TextInput::make('children_18_care_count')
                                ->label(__('field.children_18_care_count'))
                                ->placeholder(__('placeholder.number'))
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(99),

                            TextInput::make('children_accompanying_count')
                                ->label(__('field.children_accompanying_count'))
                                ->placeholder(__('placeholder.number'))
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(99),
                        ]),
                ]),

            Repeater::make('children')
                ->label(__('field.children'))
                ->relationship('children')
                ->columnSpanFull()
                ->addActionLabel(__('beneficiary.action.add_child'))
                ->disabled(fn (Get $get) => $get('doesnt_have_children'))
                ->hidden(fn (Get $get) => $get('doesnt_have_children'))
                ->defaultItems(fn (Get $get) => $get('doesnt_have_children') ? 0 : 1)
                ->schema([
                    TextInput::make('name')
                        ->label(__('field.child_name'))
                        ->maxLength(70)
                        ->required(),

                    DatePicker::make('birthdate')
                        ->label(__('field.birthdate'))
                        ->format('d.m.Y')
                        ->afterStateUpdated(function (Set $set, $state) {
                            $set('age', rescue(
                                function () use ($state) {
                                    $age = (int) Carbon::createFromFormat('d.m.Y', (string) $state)->diffInYears(today());

                                    return $age < 1 ? '<1' : $age;
                                },
                                rescue: '–',
                                report: false
                            ));
                        })
                        ->live(),

                    TextInput::make('age')
                        ->label(__('field.age'))
                        ->disabled()
                        ->dehydrated(false),

                    Select::make('gender')
                        ->label(__('field.gender'))
                        ->placeholder(__('placeholder.select_gender'))
                        ->options(GenderShortValues::options()),

                    TextInput::make('current_address')
                        ->label(__('field.current_address'))
                        ->maxLength(70),

                    TextInput::make('status')
                        ->label(__('field.child_status'))
                        ->maxLength(70),

                    TextInput::make('workspace')
                        ->label(__('field.workspace'))
                        ->maxLength(70),
                ])
                ->columns(7)
                ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                ->collapsible(),

            Grid::make()
                ->maxWidth('3xl')
                ->hidden(fn (Get $get) => $get('doesnt_have_children'))
                ->schema([
                    Textarea::make('children_notes')
                        ->label(__('field.children_notes'))
                        ->placeholder(__('placeholder.other_relevant_details'))
                        ->disabled(fn (Get $get) => $get('doesnt_have_children'))
                        ->maxLength(500)
                        ->maxWidth('3xl')
                        ->columnSpanFull(),
                ]),
        ];
    }
}
