<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\BenefitResource\Widgets;

use App\Models\Benefit;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class BenefitTypesWidget extends BaseWidget
{
    public ?Benefit $record = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => $this->record->benefitTypes())
            ->heading('')
            ->columns([
                TextColumn::make('name')
                    ->label(__('nomenclature.labels.benefit_type_name')),
                TextColumn::make('status'),
            ]);
    }
}
