<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\MonthlyPlanResource\Pages;

use App\Actions\BackAction;
use App\Concerns\HasParentResource;
use App\Filament\Organizations\Resources\InterventionPlanResource;
use App\Filament\Organizations\Resources\MonthlyPlanResource;
use App\Forms\Components\DatePicker;
use App\Forms\Components\Repeater;
use App\Forms\Components\Select;
use App\Forms\Components\TableRepeater;
use App\Models\Service;
use App\Models\ServiceIntervention;
use App\Services\Breadcrumb\InterventionPlanBreadcrumb;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Str;

class EditMonthlyPlanServicesAndInterventions extends EditRecord
{
    use HasParentResource;

    protected static string $resource = MonthlyPlanResource::class;

    public function getBreadcrumbs(): array
    {
        return  InterventionPlanBreadcrumb::make($this->parent)
            ->getViewMonthlyPlan($this->getRecord());
    }

    public function getTitle(): string
    {
        return __('intervention_plan.headings.edit_monthly_plan_services_and_interventions_title');
    }

    protected function getRedirectUrl(): ?string
    {
        return InterventionPlanResource::getUrl('view_monthly_plan', [
            'parent' => $this->parent,
            'record' => $this->getRecord(),
            'tab' => \sprintf('-%s-tab', $this->getTabSlug()),
        ]);
    }

    protected function getTabSlug(): string
    {
        return Str::slug(__('intervention_plan.headings.services_and_interventions'));
    }

    public function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url($this->getRedirectUrl()),
        ];
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make(__('intervention_plan.headings.services_and_interventions'))
                ->schema($this->getSchema()),
        ]);
    }

    public static function getSchema(): array
    {
        return [
            Repeater::make('monthlyPlanServices')
                ->relationship('monthlyPlanServices')
                ->hiddenLabel()
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
                    Grid::make()
                        ->maxWidth('3xl')
                        ->schema([
                            Select::make('service_id')
                                ->label(__('intervention_plan.labels.service_type'))
                                ->placeholder(__('intervention_plan.placeholders.select_service'))
                                ->options(function (?int $state) {
                                    $services = Service::query()
                                        ->active()
                                        ->get()
                                        ->pluck('name', 'id');

                                    if ($state && ! isset($services[$state])) {
                                        $services[$state] = Service::find($state)->name;
                                    }

                                    return $services;
                                })
                                ->required()
                                ->live(),

                            TextInput::make('institution')
                                ->label(__('intervention_plan.labels.responsible_institution'))
                                ->placeholder(__('intervention_plan.placeholders.responsible_institution'))
                                ->default(Filament::getTenant()->name)
                                ->maxLength(200),

                            TextInput::make('responsible_person')
                                ->label(__('intervention_plan.labels.responsible_person'))
                                ->placeholder(__('intervention_plan.placeholders.responsible_person'))
                                ->columnSpanFull()
                                ->maxLength(200),

                            DatePicker::make('start_date')
                                ->displayFormat('d-m-Y')
                                ->label(__('intervention_plan.labels.monthly_plan_service_interval_start')),

                            DatePicker::make('end_date')
                                ->displayFormat('d-m-Y')
                                ->label(__('intervention_plan.labels.monthly_plan_service_interval_end')),

                            Textarea::make('objective')
                                ->label(__('intervention_plan.labels.service_objective'))
                                ->placeholder(__('intervention_plan.placeholders.service_objective'))
                                ->columnSpanFull()
                                ->maxLength(2000),
                        ]),

                    TableRepeater::make('monthlyPlanInterventions')
                        ->relationship('monthlyPlanInterventions')
                        ->hiddenLabel()
                        ->hideLabels()
                        ->addActionLabel(__('intervention_plan.actions.add_intervention_repeater'))
                        ->schema([
                            Placeholder::make('number')
                                ->label(__('intervention_plan.labels.count'))
                                ->hiddenLabel()
                                ->content(function () {
                                    static $index = 0;

                                    return ++$index;
                                }),

                            Select::make('service_intervention_id')
                                ->label(__('intervention_plan.headings.interventions'))
                                ->placeholder(__('intervention_plan.placeholders.select_intervention'))
                                ->options(
                                    function (Get $get, ?int $state) {
                                        if (! $get('../../service_id')) {
                                            return [];
                                        }

                                        $interventions = Service::find((int) $get('../../service_id'))
                                            ->load('activeServiceInterventions')
                                            ->activeServiceInterventions
                                            ->pluck('name', 'id');

                                        if ($state && ! isset($interventions[$state])) {
                                            $interventions[$state] = ServiceIntervention::find($state)->name;
                                        }

                                        return $interventions;
                                    }
                                ),

                            TextInput::make('objections')
                                ->label(__('intervention_plan.labels.objections'))
                                ->placeholder(__('intervention_plan.placeholders.monthly_plan_objective'))
                                ->maxLength(200),

                            TextInput::make('observations')
                                ->label(__('intervention_plan.labels.observations'))
                                ->placeholder(__('intervention_plan.placeholders.monthly_plan_observations'))
                                ->maxLength(200),
                        ]),

                    Textarea::make('service_details')
                        ->label(__('intervention_plan.labels.service_details'))
                        ->placeholder(__('intervention_plan.placeholders.service_details')),
                ]),
        ];
    }
}
