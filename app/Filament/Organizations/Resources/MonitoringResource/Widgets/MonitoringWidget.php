<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\MonitoringResource\Widgets;

use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Models\Beneficiary;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class MonitoringWidget extends BaseWidget
{
    public ?Beneficiary $record = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn () => $this->record
                    ->monitoring()
                    ->select(['id', 'date'])
                    ->orderByDesc('date')
                    ->limit(1)
            )
            ->paginated(false)
            ->heading(__('monitoring.headings.widget_table'))
            ->headerActions([
                Tables\Actions\Action::make('view_monitoring')
                    ->label(__('general.action.view_details'))
                    ->link()
                    ->visible(fn () => $this->record->monitoring->count())
                    ->url(BeneficiaryResource::getUrl('monitorings.index', ['parent' => $this->record])),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label(__('monitoring.labels.last_monitoring_date')),
                Tables\Columns\TextColumn::make('count')
                    ->label(__('monitoring.labels.count'))
                    ->default(fn () => $this->record->monitoring()->count()),
            ])
            ->emptyStateHeading(__('monitoring.headings.widget_empty_state'))
            ->emptyStateActions([
                Tables\Actions\Action::make('create_monitoring')
                    ->label(__('monitoring.actions.create_widget'))
                    ->url(BeneficiaryResource::getUrl('monitorings.create', ['parent' => $this->record])),
            ])
            ->emptyStateIcon('heroicon-o-clipboard-document-check');
    }
}
