<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\InterventionServiceResource\Widgets;

use App\Filament\Organizations\Resources\BeneficiaryInterventionResource;
use App\Filament\Organizations\Resources\InterventionServiceResource;
use App\Models\InterventionService;
use Filament\Forms\Components\Hidden;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class InterventionsWidget extends BaseWidget
{
    public ?InterventionService $record = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => $this->record->beneficiaryInterventions()
                ->with('user')
                ->withCount('meetings'))
            ->heading(__('intervention_plan.headings.interventions'))
            ->columns([
                Tables\Columns\TextColumn::make('organizationServiceIntervention.serviceInterventionWithoutStatusCondition.name')
                    ->label(__('intervention_plan.labels.intervention')),

                Tables\Columns\TextColumn::make('user.full_name')
                    ->label(__('intervention_plan.labels.specialist')),

                Tables\Columns\TextColumn::make('interval')
                    ->label(__('intervention_plan.labels.interval')),

                Tables\Columns\TextColumn::make('meetings_count')
                    ->label(__('intervention_plan.labels.meetings_count')),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('intervention_plan.actions.add_intervention'))
                    ->createAnother(false)
                    ->modalHeading(__('intervention_plan.headings.add_intervention', ['name' => $this->record->organizationServiceWithoutStatusCondition->serviceWithoutStatusCondition->name]))
                    ->form([
                        Hidden::make('intervention_service_id')
                            ->default($this->record->id),
                        ...BeneficiaryInterventionResource::getSchema($this->record->organization_service_id),
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(__('general.action.view_details'))
                    ->url(fn ($record) => InterventionServiceResource::getUrl('view_intervention', [
                        'parent' => $this->record,
                        'record' => $record,
                    ])),
            ])
            ->emptyStateHeading(__('intervention_plan.headings.empty_state_service_intervention_table'))
            ->emptyStateDescription(__('intervention_plan.labels.empty_state_service_intervention_table'))
            ->emptyStateIcon('heroicon-o-document');
    }

    public function getDisplayName(): string
    {
        return __('intervention_plan.headings.interventions');
    }
}
