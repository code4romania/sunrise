<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\InterventionServiceResource\Widgets;

use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;
use App\Filament\Organizations\Resources\BeneficiaryInterventionResource;
use App\Filament\Organizations\Resources\InterventionServiceResource;
use App\Models\BeneficiaryIntervention;
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
                ->with('specialist')
                ->withCount('meetings'))
            ->heading(__('intervention_plan.headings.interventions'))
            ->columns([
                TextColumn::make('organizationServiceIntervention.serviceInterventionWithoutStatusCondition.name')
                    ->label(__('intervention_plan.labels.intervention')),

                TextColumn::make('specialist.name_role')
                    ->label(__('intervention_plan.labels.specialist')),

                TextColumn::make('interval')
                    ->label(__('intervention_plan.labels.interval')),

                TextColumn::make('meetings_count')
                    ->label(__('intervention_plan.labels.meetings_count')),
            ])
            ->headerActions([
                \Filament\Actions\CreateAction::make()
                    ->label(__('intervention_plan.actions.add_intervention'))
                    ->createAnother(false)
                    ->modalHeading(__('intervention_plan.headings.add_intervention', ['name' => $this->record->organizationServiceWithoutStatusCondition?->serviceWithoutStatusCondition->name]))
                    ->schema([
                        Hidden::make('intervention_service_id')
                            ->default($this->record->id),
                        ...BeneficiaryInterventionResource::getSchema($this->record->beneficiary, $this->record->organization_service_id),
                    ]),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label(__('general.action.view_details'))
                    ->url(fn (BeneficiaryIntervention $record) => InterventionServiceResource::getUrl('view_meetings', [
                        'parent' => $this->record,
                        'record' => $record,
                    ])),
            ])
            ->recordUrl(fn (BeneficiaryIntervention $record) => InterventionServiceResource::getUrl('view_meetings', [
                'parent' => $this->record,
                'record' => $record,
            ]))
            ->emptyStateHeading(__('intervention_plan.headings.empty_state_service_intervention_table'))
            ->emptyStateDescription(__('intervention_plan.labels.empty_state_service_intervention_table'))
            ->emptyStateIcon('heroicon-o-document');
    }

    public function getDisplayName(): string
    {
        return __('intervention_plan.headings.interventions');
    }
}
