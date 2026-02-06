<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages\InterventionPlan\Widgets;

use App\Models\Beneficiary;
use App\Models\InterventionPlanResult;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class InterventionPlanResultsWidget extends TableWidget
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
                    ? $plan->results()->with(['result', 'user'])
                    : InterventionPlanResult::query()->whereRaw('1 = 0')
            )
            ->heading(__('intervention_plan.headings.results_table'))
            ->columns([
                TextColumn::make('result.name')
                    ->label(__('intervention_plan.headings.result')),
                TextColumn::make('user.full_name')
                    ->label(__('intervention_plan.headings.specialist')),
                TextColumn::make('started_at')
                    ->label(__('intervention_plan.headings.started_at'))
                    ->date('d.m.Y'),
                TextColumn::make('ended_at')
                    ->label(__('intervention_plan.headings.ended_at'))
                    ->formatStateUsing(fn (InterventionPlanResult $record) => $record->ended_at?->format('d.m.Y') ?? '—'),
                TextColumn::make('observations')
                    ->label(__('intervention_plan.headings.observations'))
                    ->html()
                    ->limit(50),
            ])
            ->emptyStateHeading(__('intervention_plan.headings.empty_state_result_table'))
            ->emptyStateDescription(__('intervention_plan.labels.empty_state_result_table'))
            ->emptyStateIcon('heroicon-o-document');
    }

    public static function canView(): bool
    {
        return true;
    }
}
