<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages\InterventionPlan\Widgets;

use App\Models\Beneficiary;
use App\Models\BenefitService;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Collection as BaseCollection;

class InterventionPlanBenefitsWidget extends TableWidget
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
                    ? $plan->benefits()->with('benefit.benefitTypes')
                    : BenefitService::query()->whereRaw('1 = 0')
            )
            ->heading(__('intervention_plan.headings.benefit_services'))
            ->columns([
                TextColumn::make('benefit.name')
                    ->label(__('intervention_plan.headings.benefit_name')),
                TextColumn::make('benefit_types')
                    ->label(__('intervention_plan.headings.benefit_type'))
                    ->formatStateUsing(fn ($state, BenefitService $record): string => self::formatBenefitTypes($record)),
                TextColumn::make('award_methods')
                    ->label(__('intervention_plan.headings.award_methods'))
                    ->formatStateUsing(fn ($state): string => $state instanceof BaseCollection ? $state->map(fn ($e) => $e->getLabel())->join(', ') : '—'),
                TextColumn::make('description')
                    ->label(__('intervention_plan.headings.benefit_description'))
                    ->html()
                    ->limit(50),
            ])
            ->emptyStateHeading(__('intervention_plan.headings.empty_state_benefit_table'))
            ->emptyStateDescription(__('intervention_plan.labels.empty_state_benefit_table'))
            ->emptyStateIcon('heroicon-o-document');
    }

    private static function formatBenefitTypes(BenefitService $record): string
    {
        $types = $record->benefit?->benefitTypes;
        if (! $types || $types->isEmpty()) {
            return '—';
        }
        $ids = is_array($record->benefit_types) ? $record->benefit_types : [];
        if ($ids === []) {
            return '—';
        }

        return $types->whereIn('id', $ids)->pluck('name')->join(', ');
    }

    public static function canView(): bool
    {
        return true;
    }
}
