<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages\Widgets;

use App\Filament\Organizations\Resources\Cases\Resources\Monitoring\MonitoringResource;
use App\Models\Beneficiary;
use App\Models\Monitoring;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class MonitoringWidget extends TableWidget
{
    public ?Beneficiary $record = null;

    protected static ?string $heading = null;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $record = $this->record;

        return $table
            ->query(
                $record
                    ? $record->monitoring()->with(['specialistsTeam.user', 'specialistsTeam.roleForDisplay'])->getQuery()
                    : Monitoring::query()->whereRaw('1 = 0')
            )
            ->heading(__('monitoring.headings.widget_table'))
            ->columns([
                TextColumn::make('number')
                    ->label(__('monitoring.headings.file_number'))
                    ->sortable(),
                TextColumn::make('date')
                    ->label(__('monitoring.headings.date'))
                    ->date('Y-m-d')
                    ->sortable(),
                TextColumn::make('interval')
                    ->label(__('monitoring.headings.interval')),
                TextColumn::make('specialistsTeam.name_role')
                    ->label(__('monitoring.headings.team'))
                    ->listWithLineBreaks(),
            ])
            ->defaultSort('id', 'desc')
            ->headerActions([
                \Filament\Actions\Action::make('create_modal')
                    ->label(__('monitoring.actions.create'))
                    ->modalHeading(__('monitoring.headings.modal_create'))
                    ->modalDescription(__('monitoring.labels.modal_create_description'))
                    ->modalSubmitAction(
                        \Filament\Actions\Action::make('create_from_last')
                            ->label(__('monitoring.actions.create_from_last'))
                            ->url(fn (): string => MonitoringResource::getUrl('create', ['beneficiary' => $record]).'?copyLastFile=1')
                    )
                    ->modalCancelAction(
                        \Filament\Actions\Action::make('create_simple')
                            ->label(__('monitoring.actions.create_simple'))
                            ->outlined()
                            ->url(fn (): string => MonitoringResource::getUrl('create', ['beneficiary' => $record]))
                    )
                    ->visible(fn (): bool => $record !== null && $record->monitoring()->count() > 0),
                \Filament\Actions\Action::make('create_direct')
                    ->label(__('monitoring.actions.create'))
                    ->url(fn (): string => MonitoringResource::getUrl('create', ['beneficiary' => $record]))
                    ->visible(fn (): bool => $record !== null && $record->monitoring()->count() === 0),
            ])
            ->recordUrl(
                fn (Monitoring $monitoring): string => MonitoringResource::getUrl('view', [
                    'beneficiary' => $record,
                    'record' => $monitoring,
                ])
            )
            ->emptyStateHeading(__('monitoring.headings.empty_state_table'))
            ->emptyStateDescription(__('monitoring.labels.empty_state_table'))
            ->emptyStateIcon('heroicon-o-document');
    }

    public static function canView(): bool
    {
        return true;
    }
}
