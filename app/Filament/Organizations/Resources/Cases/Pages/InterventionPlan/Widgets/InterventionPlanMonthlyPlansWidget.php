<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages\InterventionPlan\Widgets;

use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Models\Beneficiary;
use App\Models\MonthlyPlan;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class InterventionPlanMonthlyPlansWidget extends TableWidget
{
    public ?Beneficiary $record = null;

    protected static ?string $heading = null;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $plan = $this->record?->interventionPlan;

        return $table
            ->query(
                $plan
                    ? $plan->monthlyPlans()
                        ->with(['caseManager', 'beneficiary'])
                        ->withCount(['monthlyPlanServices', 'monthlyPlanInterventions'])
                        ->getQuery()
                    : MonthlyPlan::query()->whereRaw('1 = 0')
            )
            ->heading(__('intervention_plan.headings.monthly_plans'))
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
            ->headerActions([
                \Filament\Actions\Action::make('create_modal')
                    ->label(__('intervention_plan.actions.create_monthly_plan'))
                    ->modalHeading(__('intervention_plan.headings.create_monthly_plan_modal'))
                    ->modalDescription(__('intervention_plan.labels.create_monthly_plan_modal'))
                    ->modalSubmitAction(
                        \Filament\Actions\Action::make('create_from_last')
                            ->label(__('intervention_plan.actions.create_monthly_plan_from_last'))
                            ->url(fn (): string => CaseResource::getUrl('create_monthly_plan', [
                                'case' => $this->record,
                                'copyLastPlan' => '1',
                            ]))
                    )
                    ->modalCancelAction(
                        \Filament\Actions\Action::make('create_simple')
                            ->label(__('intervention_plan.actions.create_monthly_plan_simple'))
                            ->outlined()
                            ->url(fn (): string => CaseResource::getUrl('create_monthly_plan', ['case' => $this->record]))
                    )
                    ->visible(fn (): bool => (bool) ($plan && $plan->monthlyPlans()->count() > 0)),
                \Filament\Actions\Action::make('create_direct')
                    ->label(__('intervention_plan.actions.create_monthly_plan'))
                    ->url(fn (): string => CaseResource::getUrl('create_monthly_plan', ['case' => $this->record]))
                    ->visible(fn (): bool => (bool) ($plan && $plan->monthlyPlans()->count() === 0)),
            ])
            ->recordUrl(
                fn (MonthlyPlan $monthlyPlan): string => CaseResource::getUrl('view_monthly_plan', [
                    'record' => $this->record,
                    'monthlyPlan' => $monthlyPlan,
                ])
            )
            ->emptyStateHeading(__('intervention_plan.headings.empty_state_monthly_plan_table'))
            ->emptyStateDescription(__('intervention_plan.labels.empty_state_monthly_plan_table'))
            ->emptyStateIcon('heroicon-o-document');
    }

    public static function canView(): bool
    {
        return true;
    }
}
