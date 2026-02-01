<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Benefits\Tables;

use App\Filament\Admin\Resources\Benefits\BenefitResource;
use App\Models\Benefit;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BenefitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->modifyQueryUsing(fn (Builder $query) => $query->orderBy('sort'))
            ->columns([
                TextColumn::make('name')
                    ->label(__('nomenclature.labels.benefit_name'))
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
                    ->url(fn (Benefit $record) => BenefitResource::getUrl('view', ['record' => $record])),
                EditAction::make()
                    ->url(fn (Benefit $record) => BenefitResource::getUrl('edit', ['record' => $record])),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('nomenclature.actions.add_benefit'))
                    ->url(BenefitResource::getUrl('create')),
            ])
            ->heading(__('nomenclature.headings.benefit_table'))
            ->emptyStateHeading(__('nomenclature.headings.empty_state_benefit_table'))
            ->emptyStateIcon('heroicon-o-clipboard-document-check');
    }
}
