<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Benefits\Schemas;

use App\Filament\Admin\Resources\Benefits\BenefitResource;
use App\Models\Benefit;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class BenefitInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('nomenclature.headings.benefits'))
                    ->columnSpanFull()
                    ->headerActions([
                        EditAction::make()
                            ->url(fn (Benefit $record) => BenefitResource::getUrl('edit', ['record' => $record])),
                    ])
                    ->schema([
                        Grid::make(2)
                            ->columnSpanFull()
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('nomenclature.labels.benefit_name')),
                                TextEntry::make('status')
                                    ->label(__('nomenclature.labels.status'))
                                    ->badge(),
                            ]),
                    ]),

                Section::make(__('nomenclature.headings.benefit_types'))
                    ->schema([
                        RepeatableEntry::make('benefitTypes')
                            ->hiddenLabel()
                            ->table([
                                TableColumn::make(__('nomenclature.labels.nr')),
                                TableColumn::make(__('nomenclature.labels.benefit_type_name')),
                                TableColumn::make(__('nomenclature.labels.status')),
                            ])
                            ->schema([
                                TextEntry::make('nr')
                                    ->state(function (TextEntry $entry): int {
                                        $container = $entry->getContainer();
                                        $path = $container->getStatePath();
                                        $key = Str::afterLast($path, '.');

                                        return is_numeric($key) ? (int) $key + 1 : 1;
                                    }),
                                TextEntry::make('name'),
                                TextEntry::make('status')
                                    ->badge(),
                            ]),
                    ]),
            ]);
    }
}
