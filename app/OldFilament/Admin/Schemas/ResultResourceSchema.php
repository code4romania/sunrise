<?php

declare(strict_types=1);

namespace App\Filament\Admin\Schemas;

use App\Enums\GeneralStatus;
use App\Tables\Actions\EditAction;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ResultResourceSchema
{
    public static function form(Schema $schema): Schema
    {
        return $schema->components(self::getFormComponents());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading(__('nomenclature.headings.results_table'))
            ->headerActions(self::getTableHeaderActions())
            ->columns(self::getTableColumns())
            ->filters(self::getTableFilters())
            ->recordActions(self::getTableActions())
            ->emptyStateHeading(__('nomenclature.headings.empty_state_results_table'))
            ->emptyStateDescription(null)
            ->emptyStateIcon('heroicon-o-clipboard-document-check');
    }

    public static function getFormComponents(): array
    {
        return [
            Section::make()
                ->maxWidth('3xl')
                ->description(__('nomenclature.helper_texts.result'))
                ->schema([
                    TextInput::make('name')
                        ->label(__('nomenclature.labels.result_name'))
                        ->maxLength(200),
                ]),
        ];
    }

    public static function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label(__('nomenclature.labels.result_name')),

            TextColumn::make('institutions_count')
                ->label(__('nomenclature.labels.institutions')),

            TextColumn::make('organizations_count')
                ->label(__('nomenclature.labels.centers')),

            TextColumn::make('status')
                ->label(__('nomenclature.labels.status')),
        ];
    }

    public static function getTableFilters(): array
    {
        return [
            SelectFilter::make('status')
                ->options(GeneralStatus::options()),
        ];
    }

    public static function getTableActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    public static function getTableHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('nomenclature.actions.add_result')),
        ];
    }
}
