<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Results\Schemas;

use App\Filament\Admin\Resources\Results\ResultResource;
use App\Models\Result;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ResultInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('nomenclature.headings.results'))
                    ->columnSpanFull()
                    ->headerActions([
                        EditAction::make()
                            ->url(fn (Result $record) => ResultResource::getUrl('edit', ['record' => $record])),
                    ])
                    ->schema([
                        Grid::make(2)
                            ->columnSpanFull()
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('nomenclature.labels.result_name')),
                                TextEntry::make('status')
                                    ->label(__('nomenclature.labels.status'))
                                    ->badge(),
                            ]),
                    ]),
            ]);
    }
}
