<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages\InterventionPlan\Widgets;

use App\Models\Beneficiary;
use App\Models\InterventionService;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class InterventionPlanServicesWidget extends TableWidget
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
                    ? $plan->services()
                        ->with(['organizationServiceWithoutStatusCondition.serviceWithoutStatusCondition', 'specialist.user', 'specialist.role'])
                        ->withCount(['beneficiaryInterventions', 'meetings'])
                    : InterventionService::query()->whereRaw('1 = 0')
            )
            ->heading(__('intervention_plan.headings.services'))
            ->columns([
                TextColumn::make('organization_service_id')
                    ->label(__('intervention_plan.labels.service'))
                    ->formatStateUsing(fn (InterventionService $record) => $record->organizationServiceWithoutStatusCondition?->serviceWithoutStatusCondition?->name ?? '—'),
                TextColumn::make('specialist.name_role')
                    ->label(__('intervention_plan.labels.specialist')),
                TextColumn::make('beneficiary_interventions_count')
                    ->label(__('intervention_plan.labels.interventions_count')),
                TextColumn::make('meetings_count')
                    ->label(__('intervention_plan.labels.meetings_count')),
            ])
            ->emptyStateHeading(__('intervention_plan.headings.empty_state_service_table'))
            ->emptyStateDescription(__('intervention_plan.labels.empty_state_service_table'))
            ->emptyStateIcon('heroicon-o-document');
    }

    public static function canView(): bool
    {
        return true;
    }
}
