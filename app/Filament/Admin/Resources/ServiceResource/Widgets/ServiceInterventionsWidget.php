<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ServiceResource\Widgets;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;
use stdClass;

class ServiceInterventionsWidget extends BaseWidget
{
    public ?Model $record = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn () => $this->record->serviceInterventions()
            )
            ->columns([
                TextColumn::make('number')
                    ->label(__('nomenclature.labels.nr'))
                    ->state(fn (stdClass $rowLoop) => $rowLoop->iteration),
                TextColumn::make('name')
                    ->label(__('nomenclature.labels.name')),
                TextColumn::make('institutions')
                    ->label(__('nomenclature.labels.institutions')),
                TextColumn::make('centers'),
                TextColumn::make('status')
                    ->label(__('nomenclature.labels.status'))
                    ->badge(),
            ])
            ->heading(__('nomenclature.headings.service_intervention'));
    }
}
