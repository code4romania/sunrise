<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Results\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ResultForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                    ->columnSpanFull()
                    ->schema([
                        Section::make()
                            ->hiddenLabel()
                            ->description(__('nomenclature.helper_texts.result'))
                            ->columns(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('nomenclature.labels.result_name'))
                                    ->placeholder(__('nomenclature.labels.result_name'))
                                    ->required()
                                    ->maxLength(200)
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }
}
