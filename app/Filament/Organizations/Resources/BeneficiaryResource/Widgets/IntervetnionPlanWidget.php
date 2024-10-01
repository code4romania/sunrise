<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Widgets;

use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Models\Beneficiary;
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
            ->query(fn () => $this->record->interventionPlans())
            ->columns([
                TextColumn::make('plan_date'),
            ])
            ->heading(__('intervention_plan.headings.table'))
            ->actions([
                ViewAction::make('view_intervention_plan')
                    ->url(fn ($record) => BeneficiaryResource::getUrl('view_intervention_plan', [
                        //                        ''
                        'parent' => $this->record,
                        'record' => $record,
                    ])),
            ])
            ->emptyStateHeading(__('intervention_plan.headings.empty_state_table'))
            ->emptyStateDescription(__('intervention_plan.labels.empty_state_table'))
            ->emptyStateIcon('heroicon-o-presentation-chart-bar')
            ->emptyStateActions([
                Action::make('create_intervention_plan')
                    ->label(__('intervention_plan.actions.create'))
                    ->outlined()
                    ->action(function () {
                        $this->redirect(
                            BeneficiaryResource::getUrl('view_intervention_plan', [
                                'parent' => $this->record,
                                'record' => InterventionPlan::create([
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
