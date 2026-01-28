<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\InterventionPlanResource\Widgets;

use App\Filament\Organizations\Resources\InterventionPlanResource;
use App\Models\InterventionPlan;
use App\Models\MonthlyPlan;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class MonthlyPlanWidget extends BaseWidget
{
    public ?InterventionPlan $record = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn () => $this->record->monthlyPlans()
                    ->with(['caseManager', 'beneficiary'])
                    ->withCount(['monthlyPlanServices', 'monthlyPlanInterventions'])
            )
            ->heading(__('intervention_plan.headings.monthly_plans'))
            ->headerActions([
                \Filament\Actions\Action::make('create_modal')
                    ->label(__('intervention_plan.actions.create_monthly_plan'))
                    ->modalHeading(__('intervention_plan.headings.create_monthly_plan_modal'))
                    ->modalDescription(__('intervention_plan.labels.create_monthly_plan_modal'))
                    ->modalSubmitAction(
                        \Filament\Actions\Action::make('crete_from_last')
                            ->label(__('intervention_plan.actions.create_monthly_plan_from_last'))
                            ->url(InterventionPlanResource::getUrl('create_monthly_plan', [
                                'parent' => $this->record,
                                'copyLastPlan' => 'copyLastPlan',
                            ]))
                    )
                    ->modalCancelAction(
                        \Filament\Actions\Action::make('create_simple')
                            ->label(__('intervention_plan.actions.create_monthly_plan_simple'))
                            ->outlined()
                            ->url(InterventionPlanResource::getUrl('create_monthly_plan', ['parent' => $this->record]))
                    )
                    ->visible(fn () => $this->record->monthlyPlans->count()),

                \Filament\Actions\CreateAction::make()
                    ->label(__('intervention_plan.actions.create_monthly_plan'))
                    ->url(InterventionPlanResource::getUrl('create_monthly_plan', ['parent' => $this->record]))
                    ->visible(fn () => ! $this->record->monthlyPlans->count()),
            ])
            ->columns([
                TextColumn::make('interval')
                    ->label(__('intervention_plan.headings.interval')),

                TextColumn::make('caseManager.full_name')
                    ->label(__('intervention_plan.headings.case_manager')),

                TextColumn::make('monthly_plan_services_count')
                    ->label(__('intervention_plan.headings.services_count')),

                TextColumn::make('monthly_plan_interventions_count')
                    ->label(__('intervention_plan.headings.interventions_count')),
            ])
            ->recordActions([
                \Filament\Actions\ViewAction::make()
                    ->label(__('general.action.view_details'))
                    ->url(fn (MonthlyPlan $record) => InterventionPlanResource::getUrl('view_monthly_plan', [
                        'parent' => $this->record,
                        'record' => $record,
                    ])),
            ])
            ->recordUrl(
                fn (MonthlyPlan $record) => InterventionPlanResource::getUrl('view_monthly_plan', [
                    'parent' => $this->record,
                    'record' => $record,
                ])
            )
            ->emptyStateHeading(__('intervention_plan.headings.empty_state_monthly_plan_table'))
            ->emptyStateDescription(__('intervention_plan.labels.empty_state_monthly_plan_table'))
            ->emptyStateIcon('heroicon-o-document');
    }

    public function getDisplayName(): string
    {
        return __('intervention_plan.headings.monthly_plans');
    }
}
