<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages\InterventionPlan\Widgets;

use App\Forms\Components\DatePicker;
use App\Models\Beneficiary;
use App\Models\MonthlyPlan;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Carbon;

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
                \Filament\Actions\CreateAction::make()
                    ->label(__('intervention_plan.actions.create_monthly_plan'))
                    ->modalHeading(__('intervention_plan.headings.create_monthly_plan_modal'))
                    ->model(MonthlyPlan::class)
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['intervention_plan_id'] = $this->record?->interventionPlan?->id;

                        return $data;
                    })
                    ->form([
                        DatePicker::make('start_date')
                            ->label(__('intervention_plan.labels.monthly_plan_service_interval_start'))
                            ->default(fn () => Carbon::now()->startOfMonth()->format('Y-m-d'))
                            ->required(),
                        DatePicker::make('end_date')
                            ->label(__('intervention_plan.labels.monthly_plan_service_interval_end'))
                            ->default(fn () => Carbon::now()->endOfMonth()->format('Y-m-d'))
                            ->required(),
                        Select::make('case_manager_user_id')
                            ->label(__('intervention_plan.headings.case_manager'))
                            ->options(User::getTenantOrganizationUsers()->all())
                            ->default(fn () => auth()->id()),
                    ])
                    ->createAnother(false),
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
