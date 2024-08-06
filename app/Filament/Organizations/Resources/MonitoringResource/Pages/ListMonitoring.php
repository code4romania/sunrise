<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\MonitoringResource\Pages;

use App\Concerns\HasParentResource;
use App\Filament\Organizations\Resources\MonitoringResource;
use App\Models\Monitoring;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class ListMonitoring extends ListRecords
{
    use HasParentResource;

    protected static string $resource = MonitoringResource::class;

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->parent)->getBreadcrumbsForMonitoring();
    }

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.section.monitoring.heading.list');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->url(fn () => self::getParentResource()::getUrl('monitorings.create', [
                    'parent' => $this->parent,
                ])),
        ];
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id'),
            TextColumn::make('number'),
            TextColumn::make('date'),
            TextColumn::make('start_date')
                ->formatStateUsing(fn ($record) => $record->start_date . ' - ' . $record->end_date),
            TextColumn::make('team'),
        ])
            ->actions([
                ViewAction::make()
                    ->url(fn (Monitoring $record) => (static::getParentResource()::getUrl('monitorings.view', [
                        'parent' => $this->parent,
                        'record' => $record,
                    ]))),
            ])
            ->emptyStateHeading('aaaaaa');
    }
}
