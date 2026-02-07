<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Results\Tables;

use App\Filament\Admin\Resources\Results\ResultResource;
use App\Models\Result;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ResultsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->modifyQueryUsing(fn (Builder $query) => $query->orderBy('sort'))
            ->columns([
                TextColumn::make('name')
                    ->label(__('nomenclature.labels.result_name'))
                    ->searchable(),
                TextColumn::make('institutions_count')
                    ->label(__('nomenclature.labels.institutions')),
                TextColumn::make('organizations_count')
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
                    ->url(fn (Result $record) => ResultResource::getUrl('view', ['record' => $record])),
                EditAction::make()
                    ->url(fn (Result $record) => ResultResource::getUrl('edit', ['record' => $record])),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('nomenclature.actions.add_result'))
                    ->url(ResultResource::getUrl('create')),
            ])
            ->heading(__('nomenclature.headings.results_table'))
            ->emptyStateHeading(__('nomenclature.headings.empty_state_results_table'))
            ->emptyStateIcon('heroicon-o-clipboard-document-check');
    }
}
