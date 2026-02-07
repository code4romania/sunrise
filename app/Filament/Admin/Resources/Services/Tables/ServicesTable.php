<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Services\Tables;

use App\Filament\Admin\Resources\Services\ServiceResource;
use App\Models\Service;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount(['organizationServices'])->orderBy('sort'))
            ->columns([
                TextColumn::make('name')
                    ->label(__('nomenclature.labels.name'))
                    ->searchable(),
                TextColumn::make('institutions_count')
                    ->label(__('nomenclature.labels.institutions')),
                TextColumn::make('organization_services_count')
                    ->label(__('nomenclature.labels.centers')),
                TextColumn::make('status')
                    ->label(__('nomenclature.labels.status'))
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(\App\Enums\GeneralStatus::options()),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn (Service $record) => ServiceResource::getUrl('view', ['record' => $record])),
                EditAction::make()
                    ->url(fn (Service $record) => ServiceResource::getUrl('edit', ['record' => $record])),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('nomenclature.actions.add_service'))
                    ->url(ServiceResource::getUrl('create')),
            ])
            ->heading(__('nomenclature.headings.service_table'))
            ->emptyStateHeading(__('nomenclature.labels.empty_state_service_table'))
            ->emptyStateIcon('heroicon-o-clipboard-document-check');
    }
}
