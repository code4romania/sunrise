<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages\InterventionPlan\Widgets;

use App\Models\Beneficiary;
use App\Models\InterventionPlanResult;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class InterventionPlanParticipationWidget extends TableWidget
{
    public ?Beneficiary $record = null;

    protected static ?string $heading = null;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(InterventionPlanResult::query()->whereRaw('1 = 0'))
            ->heading(__('intervention_plan.headings.social_service_participation'))
            ->columns([
                TextColumn::make('id')->label(' '),
            ])
            ->emptyStateHeading(__('intervention_plan.headings.social_service_participation'))
            ->emptyStateDescription(__('intervention_plan.labels.social_service_participation_empty'))
            ->emptyStateIcon('heroicon-o-user-group');
    }

    public static function canView(): bool
    {
        return true;
    }
}
