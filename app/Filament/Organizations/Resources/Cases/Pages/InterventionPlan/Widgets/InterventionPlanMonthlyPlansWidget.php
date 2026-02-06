<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages\InterventionPlan\Widgets;

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
            ->emptyStateHeading(__('intervention_plan.headings.empty_state_monthly_plan_table'))
            ->emptyStateDescription(__('intervention_plan.labels.empty_state_monthly_plan_table'))
            ->emptyStateIcon('heroicon-o-document');
    }

    public static function canView(): bool
    {
        return true;
    }
}
