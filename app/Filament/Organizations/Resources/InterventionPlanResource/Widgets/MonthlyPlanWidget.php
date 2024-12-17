<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\InterventionPlanResource\Widgets;

use App\Filament\Organizations\Resources\InterventionPlanResource;
use App\Models\InterventionPlan;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class MonthlyPlanWidget extends BaseWidget
{
    public ?InterventionPlan $record = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => $this->record->monthlyPlans())
            ->heading(__('intervention_plan.headings.monthly_plans'))
            ->headerActions([
                CreateAction::make()
                    ->label(__('intervention_plan.actions.create_monthly_plan'))
                    ->url(fn () => InterventionPlanResource::getUrl('create_monthly_plan', ['parent' => $this->record])),
            ])
            ->columns([
                TextColumn::make('interval'),
                TextColumn::make('caseManager.full_name'),
                TextColumn::make('service_count'),
                TextColumn::make('intervention_count'),
            ]);
    }

    public function getDisplayName(): string
    {
        return __('intervention_plan.headings.monthly_plans');
    }
}
