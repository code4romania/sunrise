<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\BenefitResource\Widgets;

use App\Enums\GeneralStatus;
use App\Filament\Admin\Resources\BenefitResource;
use App\Models\Benefit;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class BenefitWidget extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Benefit::query()
            )
            ->headerActions([
                CreateAction::make()
                    ->label(__('nomenclature.actions.add_benefit'))
                    ->url(BenefitResource::getUrl('create')),
            ])
            ->heading(__('nomenclature.headings.benefit_table'))
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('status'),
            ])
            ->actions([
                ViewAction::make()
                    ->label(__('general.action.view_details'))
                    ->url(fn (Benefit $record) => BenefitResource::getUrl('view', ['record' => $record])),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(GeneralStatus::options()),
            ])
            ->emptyStateHeading(__('nomenclature.headings.empty_state_benefit_table'))
            ->emptyStateDescription(null)
            ->emptyStateIcon('heroicon-o-clipboard-document-check');
    }

    public function getDisplayName(): string
    {
        return 'Beneficii';
    }
}
