<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\MonthlyPlanResource\Pages;

use App\Concerns\HasParentResource;
use App\Filament\Organizations\Resources\InterventionPlanResource;
use App\Filament\Organizations\Resources\MonthlyPlanResource;
use App\Forms\Components\DatePicker;
use App\Forms\Components\Repeater;
use App\Forms\Components\Select;
use App\Forms\Components\TableRepeater;
use App\Models\Service;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Alignment;

class EditMonthlyPlan extends EditRecord
{
    use HasParentResource;

    protected static string $resource = MonthlyPlanResource::class;

    public function getBreadcrumbs(): array
    {
        return  [];
    }

    protected function getRedirectUrl(): ?string
    {
        return InterventionPlanResource::getUrl('view_monthly_plan', [
            'parent' => $this->parent,
            'record' => $this->getRecord(),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('details')
                ->label(__('intervention_plan.headings.monthly_plan_details'))
                ->schema([
                    Section::make()
                        ->maxWidth('3xl')
                        ->columns()
                        ->schema([
                            DatePicker::make('start_date')
                                ->label(__('intervention_plan.labels.monthly_plan_start_date'))
                                ->required(),

                            DatePicker::make('end_date')
                                ->label(__('intervention_plan.labels.monthly_plan_end_date'))
                                ->required(),

                            Select::make('case_manager_user_id')
                                ->label(__('intervention_plan.labels.case_manager'))
                                ->placeholder(__('intervention_plan.placeholders.specialist'))
                                ->options(
                                    fn () => $this->parent
                                        ->beneficiary
                                        ->specialistsMembers
                                        ->pluck('full_name', 'id')
                                )
                                ->required(),

                            Select::make('specialists')
                                ->label(__('intervention_plan.labels.specialists'))
                                ->placeholder(__('intervention_plan.placeholders.specialists'))
                                ->multiple()
                                ->options(
                                    fn () => $this->parent
                                        ->beneficiary
                                        ->specialistsTeam
                                        ->pluck('name_role', 'id')
                                )
                                ->required(),
                        ]),

                    Hidden::make('intervention_plan_id')
                        ->default($this->parent->id),
                ]),

            Section::make('services_and_interventions')
                ->label(__('intervention_plan.headings.services_and_interventions'))
                ->schema([
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
                                        ->relationship('service', 'name')
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
                                            fn (Get $get) => $get('../../service_id') ?
                                                Service::find((int) $get('../../service_id'))
                                                    ->load('serviceInterventions')
                                                    ->serviceInterventions
                                                    ->pluck('name', 'id') :
                                                []
                                        ),

                                    TextInput::make('objections')
                                        ->label(__('intervention_plan.labels.objections'))
                                        ->placeholder(__('intervention_plan.placeholders.monthly_plan_objections'))
                                        ->maxLength(200),

                                    TextInput::make('observations')
                                        ->label(__('intervention_plan.labels.observations'))
                                        ->placeholder(__('intervention_plan.placeholders.monthly_plan_observations'))
                                        ->maxLength(200),
                                ]),
                        ]),

                    Textarea::make('observations')
                        ->label(__('intervention_plan.labels.observations'))
                        ->placeholder(__('intervention_plan.placeholders.monthly_plan_observations')),
                ]),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
