<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages\Widgets;

use App\Filament\Organizations\Resources\Cases\Resources\Monitoring\MonitoringResource;
use App\Models\Beneficiary;
use App\Models\Monitoring;
use App\Services\CaseExports\CaseExportManager;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
                Action::make('download_all_monitoring_sheets')
                    ->label(__('monitoring.actions.download_all'))
                    ->outlined()
                    ->action(fn (): StreamedResponse => app(CaseExportManager::class)->downloadAllMonitoringPdfs($record))
                    ->visible(fn (): bool => $record !== null && $record->monitoring()->exists()),
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
