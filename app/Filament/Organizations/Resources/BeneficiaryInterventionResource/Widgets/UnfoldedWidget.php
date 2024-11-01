<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryInterventionResource\Widgets;

use App\Filament\Organizations\Resources\InterventionServiceResource;
use App\Models\BeneficiaryIntervention;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class UnfoldedWidget extends BaseWidget
{
    public ?BeneficiaryIntervention $record = null;

    protected int | string | array $columnSpan = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn () => $this->record->meetings()
                    ->with('user')
            )
            ->heading(__('intervention_plan.headings.unfolded_table'))
            ->columns([
                TextColumn::make('meet_number')
                    ->label(__('intervention_plan.labels.meet_number'))
                    ->state(static function (\stdClass $rowLoop) {
                        return $rowLoop->iteration ?? null;
                    }),
                TextColumn::make('status')
                    ->label(__('intervention_plan.labels.status')),
                TextColumn::make('date')
                    ->label(__('intervention_plan.labels.date')),
                TextColumn::make('time')
                    ->label(__('intervention_plan.labels.time')),
                TextColumn::make('duration')
                    ->label(__('intervention_plan.labels.duration')),
                TextColumn::make('user_id')
                    ->label(__('intervention_plan.labels.specialist'))
                    ->formatStateUsing(fn ($record) => $record->user?->full_name),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exports([
                        ExcelExport::make()
                            ->withColumns([
                                Column::make('status')
                                    ->heading(__('intervention_plan.labels.status')),
                                Column::make('date')
                                    ->heading(__('intervention_plan.labels.date')),
                                Column::make('time')
                                    ->heading(__('intervention_plan.labels.time')),
                                Column::make('duration')
                                    ->heading(__('intervention_plan.labels.duration')),
                                Column::make('user.full_name')
                                    ->heading(__('intervention_plan.labels.specialist')),
                                Column::make('observations')
                                    ->heading(__('intervention_plan.labels.observations')),
                            ])
                            ->useTableQuery(),
                    ]),
            ])
            ->actions([
                ViewAction::make()
                    ->label(__('general.action.view_observations'))
                    ->url(fn () => InterventionServiceResource::getUrl('view_meetings', [
                        'parent' => $this->record->interventionService,
                        'record' => $this->record,
                    ])),
            ])
            ->emptyStateHeading(__('intervention_plan.headings.empty_state_service_table'))
            ->emptyStateDescription(__('intervention_plan.labels.empty_state_service_table'))
            ->emptyStateIcon('heroicon-o-document');
    }

    public function getDisplayName(): string
    {
        return __('intervention_plan.headings.unfolded');
    }
}
