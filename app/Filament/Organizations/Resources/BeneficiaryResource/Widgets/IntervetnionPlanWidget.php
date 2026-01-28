<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Widgets;

use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Filament\Organizations\Resources\InterventionServiceResource;
use App\Models\Beneficiary;
use App\Models\BeneficiaryIntervention;
use App\Models\InterventionPlan;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class IntervetnionPlanWidget extends BaseWidget
{
    public ?Beneficiary $record = null;

    protected int | string | array $columnSpan = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn () => $this->record
                    ->interventionPlan
                    ?->beneficiaryInterventions()
                    ->with([
                        'organizationServiceIntervention.serviceIntervention',
                        'organizationServiceIntervention.organizationService.service',
                        'specialist.user',
                        'specialist.role',
                        'nextMeeting',
                        'interventionService',
                        'interventionPlan',
                        'beneficiary',
                    ])
                    ->withCount('meetings') ?:
                    $this->record->interventionPlan()
            )
            ->columns([
                TextColumn::make('organizationServiceIntervention.serviceIntervention.name')
                    ->label(__('intervention_plan.labels.intervention')),

                TextColumn::make('organizationServiceIntervention.organizationService.service.name')
                    ->label(__('intervention_plan.labels.service')),

                TextColumn::make('specialist.name_role')
                    ->label(__('intervention_plan.labels.specialist')),

                TextColumn::make('meetings_count')
                    ->label(__('intervention_plan.labels.meetings_count')),

                TextColumn::make('nextMeeting.date')
                    ->label(__('intervention_plan.labels.next_meeting')),
            ])
            ->defaultPaginationPageOption(5)
            ->paginationPageOptions([5])
            ->heading(__('intervention_plan.headings.table'))
            ->recordActions([
                \Filament\Actions\ViewAction::make('view_intervention')
                    ->label(__('intervention_plan.actions.view_intervention'))
                    ->url(fn (BeneficiaryIntervention $record) => InterventionServiceResource::getUrl('view_intervention', [
                        'beneficiary' => $record->beneficiary,
                        'intervention_plan' => $record->interventionPlan,
                        'parent' => $record->interventionService,
                        'record' => $record,
                    ])),
            ])
            ->recordUrl(fn (BeneficiaryIntervention $record) => InterventionServiceResource::getUrl('view_intervention', [
                'beneficiary' => $record->beneficiary,
                'intervention_plan' => $record->interventionPlan,
                'parent' => $record->interventionService,
                'record' => $record,
            ]))
            ->headerActions([
                \Filament\Actions\Action::make('view_intervention_plan')
                    ->label(__('intervention_plan.actions.view_intervention_plan'))
                    ->visible((bool) $this->record->interventionPlan)
                    ->link()
                    ->url(
                        fn () => BeneficiaryResource::getUrl('view_intervention_plan', [
                            'parent' => $this->record,
                            'record' => $this->record->interventionPlan,
                        ])
                    ),
            ])
            ->emptyStateHeading(
                $this->record->interventionPlan ?
                __('intervention_plan.headings.empty_state_table_without_intervetntions') :
                __('intervention_plan.headings.empty_state_table')
            )
            ->emptyStateDescription(
                $this->record->interventionPlan ?
                    __('intervention_plan.labels.empty_state_table_without_intervetntions') :
                    __('intervention_plan.labels.empty_state_table')
            )
            ->emptyStateIcon('heroicon-o-presentation-chart-bar')
            ->emptyStateActions([
                \Filament\Actions\Action::make('create_intervention_plan')
                    ->label(__('intervention_plan.actions.create'))
                    ->hidden((bool) $this->record->interventionPlan)
                    ->outlined()
                    ->action(function () {
                        $this->redirect(
                            BeneficiaryResource::getUrl('view_intervention_plan', [
                                'parent' => $this->record,
                                'record' => $this->record->interventionPlan ?? InterventionPlan::create([
                                    'beneficiary_id' => $this->record->id,
                                    'admit_date_in_center' => $this->record->created_at,
                                    'plan_date' => date('Y-m-d'),
                                ]),
                            ])
                        );
                    }),
            ]);
    }
}
