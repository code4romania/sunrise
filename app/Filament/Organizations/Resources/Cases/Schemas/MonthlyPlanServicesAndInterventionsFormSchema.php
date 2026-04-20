<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Schemas;

use App\Forms\Components\DatePicker;
use App\Forms\Components\Repeater as AppRepeater;
use App\Models\Service;
use App\Models\ServiceIntervention;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Enums\Alignment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Validator;

final class MonthlyPlanServicesAndInterventionsFormSchema
{
    public static function monthlyPlanServicesRepeater(): AppRepeater
    {
        return AppRepeater::make('monthlyPlanServices')
            ->relationship('monthlyPlanServices')
            ->afterStateHydrated(function (AppRepeater $component): void {
                $record = $component->getRecord();
                if ($record instanceof Model && $record->exists) {
                    return;
                }

                $state = $component->getState();
                if (is_array($state) && count($state) > 0) {
                    return;
                }

                $component->ensureMinItems();
            })
            ->hiddenLabel()
            ->columnSpanFull()
            ->collapsible()
            ->itemLabel(function () {
                static $index = 0;

                return __('intervention_plan.headings.service_count', [
                    'number' => ++$index,
                ]);
            })
            ->addAction(
                fn (Action $action) => $action
                    ->link()
                    ->label(__('intervention_plan.actions.add_service_repeater'))
                    ->color('primary')
            )
            ->addActionAlignment(Alignment::Left)
            ->schema([
                Grid::make(2)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('service_id')
                            ->label(__('intervention_plan.labels.service_type'))
                            ->placeholder(__('intervention_plan.placeholders.select_service'))
                            ->live()
                            ->options(function (?int $state): array {
                                $services = Service::query()
                                    ->active()
                                    ->orderBy('sort')
                                    ->get()
                                    ->pluck('name', 'id')
                                    ->all();

                                if ($state && ! isset($services[$state])) {
                                    $service = Service::query()->find($state);
                                    if ($service) {
                                        $services[$state] = $service->name;
                                    }
                                }

                                return $services;
                            })
                            ->required(),

                        TextInput::make('institution')
                            ->label(__('intervention_plan.labels.responsible_institution'))
                            ->placeholder(__('intervention_plan.placeholders.responsible_institution'))
                            ->default(Filament::getTenant()?->name)
                            ->maxLength(200),
                    ]),

                TextInput::make('responsible_person')
                    ->label(__('intervention_plan.labels.responsible_person'))
                    ->placeholder(__('intervention_plan.placeholders.responsible_person'))
                    ->columnSpanFull()
                    ->maxLength(200),

                Grid::make(2)
                    ->columnSpanFull()
                    ->schema([
                        DatePicker::make('start_date')
                            ->label(__('intervention_plan.labels.monthly_plan_service_interval_start'))
                            ->placeholder('ZZ/LL/AA'),

                        DatePicker::make('end_date')
                            ->label(__('intervention_plan.labels.monthly_plan_service_interval_end'))
                            ->placeholder('ZZ/LL/AA')
                            ->rules([
                                function (): \Closure {
                                    return function (string $attribute, mixed $value, \Closure $fail, Validator $validator): void {
                                        if (blank($value) || $value === '-') {
                                            return;
                                        }

                                        $startKey = preg_replace('/\.end_date$/', '.start_date', $attribute);
                                        $start = data_get($validator->getData(), $startKey);

                                        if (blank($start) || $start === '-') {
                                            return;
                                        }

                                        try {
                                            if (Carbon::parse($value)->lt(Carbon::parse($start))) {
                                                $fail(__('intervention_plan.validation.service_end_date_after_start'));
                                            }
                                        } catch (\Throwable) {
                                            return;
                                        }
                                    };
                                },
                            ])
                            ->validationAttribute(__('intervention_plan.labels.monthly_plan_service_interval_end')),
                    ]),

                Textarea::make('objective')
                    ->label(__('intervention_plan.labels.service_objective'))
                    ->placeholder(__('intervention_plan.placeholders.service_objective'))
                    ->columnSpanFull()
                    ->rows(4)
                    ->maxLength(2000),

                AppRepeater::make('monthlyPlanInterventions')
                    ->relationship('monthlyPlanInterventions')
                    ->hiddenLabel()
                    ->addActionLabel(__('intervention_plan.actions.add_intervention_repeater'))
                    ->addActionAlignment(Alignment::Center)
                    ->columns(12)
                    ->itemLabel(fn (array $state): ?string => isset($state['service_intervention_id']) && $state['service_intervention_id'] ? (ServiceIntervention::query()->find($state['service_intervention_id'])?->name ?? null) : null)
                    ->schema([
                        TextEntry::make('number')
                            ->label(__('intervention_plan.labels.count'))
                            ->columnSpan(1)
                            ->state(function () {
                                static $index = 0;

                                return (string) ++$index;
                            }),

                        Select::make('service_intervention_id')
                            ->label(__('intervention_plan.headings.interventions'))
                            ->placeholder(__('intervention_plan.placeholders.select_intervention'))
                            ->helperText(function (Get $get): ?string {
                                $serviceId = self::resolveMonthlyPlanServiceIdFromForm($get);
                                if ($serviceId === null) {
                                    return __('intervention_plan.helpers.select_service_first_for_interventions');
                                }

                                $service = Service::query()->find($serviceId);
                                if (! $service) {
                                    return null;
                                }

                                if ($service->activeServiceInterventions()->count() === 0) {
                                    return __('intervention_plan.helpers.no_active_interventions_for_service');
                                }

                                return null;
                            })
                            ->columnSpan(3)
                            ->options(
                                function (Get $get, ?int $state): array {
                                    $serviceId = self::resolveMonthlyPlanServiceIdFromForm($get);
                                    if ($serviceId === null) {
                                        return [];
                                    }

                                    $service = Service::query()->find($serviceId);
                                    if (! $service) {
                                        return [];
                                    }

                                    $interventions = $service->activeServiceInterventions()
                                        ->orderBy('sort')
                                        ->get()
                                        ->pluck('name', 'id')
                                        ->all();

                                    if ($state && ! isset($interventions[$state])) {
                                        $intervention = ServiceIntervention::query()->find($state);
                                        if ($intervention) {
                                            $interventions[$state] = $intervention->name;
                                        }
                                    }

                                    return $interventions;
                                }
                            ),

                        TextInput::make('objections')
                            ->label(__('intervention_plan.labels.objectives_short'))
                            ->placeholder(__('intervention_plan.placeholders.monthly_plan_objective'))
                            ->columnSpan(4)
                            ->maxLength(200),

                        TextInput::make('observations')
                            ->label(__('intervention_plan.labels.observations'))
                            ->placeholder(__('intervention_plan.placeholders.monthly_plan_observations'))
                            ->columnSpan(4)
                            ->maxLength(200),
                    ]),

                Textarea::make('service_details')
                    ->label(__('intervention_plan.labels.service_details_label'))
                    ->placeholder(__('intervention_plan.placeholders.service_details'))
                    ->columnSpanFull()
                    ->rows(4)
                    ->maxLength(2000),
            ]);
    }

    public static function resolveMonthlyPlanServiceIdFromForm(Get $get): ?int
    {
        $get->skipComponentsChildContainersWhileSearching(false);

        try {
            $raw = $get('../../service_id') ?? $get('../service_id');
        } finally {
            $get->skipComponentsChildContainersWhileSearching(true);
        }

        if ($raw === null || $raw === '') {
            return null;
        }

        return (int) $raw;
    }
}
