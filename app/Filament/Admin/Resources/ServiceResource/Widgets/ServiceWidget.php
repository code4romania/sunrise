<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ServiceResource\Widgets;

use App\Enums\GeneralStatus;
use App\Filament\Admin\Resources\ServiceResource;
use App\Models\Service;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ServiceWidget extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Service::query()
            )
            ->columns([
                TextColumn::make('name')
                    ->label(__('nomenclature.labels.name'))
                    ->searchable(),
                TextColumn::make('institutions')
                    ->label(__('nomenclature.labels.institutions')),
                TextColumn::make('centers')
                    ->label(__('nomenclature.labels.centers')),
                TextColumn::make('status')
                    ->label(__('nomenclature.labels.status'))
                    ->badge(),
            ])
            ->actions([
                ViewAction::make()
                    ->label(__('general.action.view_details'))
                    ->url(fn (Service $record) => ServiceResource::getUrl('view', ['record' => $record])),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('nomenclature.actions.add_service'))
                    ->url(ServiceResource::getUrl('create')),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(GeneralStatus::options()),
            ])
            ->heading(__('nomenclature.headings.service_table'))
            ->emptyStateHeading(__('nomenclature.labels.empty_state_service_table'))
            ->emptyStateDescription(null)
            ->emptyStateIcon('heroicon-o-clipboard-document-check');
    }

    public function getDisplayName(): string
    {
        return __('nomenclature.headings.service');
    }
}
