<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ServiceResource\Widgets;

use App\Models\Service;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class InterventionsWidget extends BaseWidget
{
    public Service | null $record = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn () => $this->record->serviceInterventions()
            )
            ->columns([
                TextColumn::make('name')
                    ->label(__('nomenclature.labels.intervention_name')),

                TextColumn::make('institutions_count')
                    ->label(__('nomenclature.labels.institutions')),

                TextColumn::make('organizations_count')
                    ->label(__('nomenclature.labels.centers')),

                TextColumn::make('status')
                    ->label(__('nomenclature.labels.status')),

            ]);
    }
}
