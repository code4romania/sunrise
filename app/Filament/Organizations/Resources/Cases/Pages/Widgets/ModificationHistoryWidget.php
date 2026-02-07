<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages\Widgets;

use App\Models\Activity;
use App\Models\Beneficiary;
use App\Services\ActivityLabelHelper;
use App\Tables\Columns\DateColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class ModificationHistoryWidget extends TableWidget
{
    public ?Beneficiary $record = null;

    protected static ?string $heading = null;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $record = $this->record;

        $query = $record
            ? Activity::query()->whereMorphedTo('subject', $record)->with('causer')
            : Activity::query()->whereRaw('1 = 0');

        return $table
            ->query($query)
            ->heading(__('beneficiary.section.history.headings.table'))
            ->columns([
                DateColumn::make('created_at')
                    ->label(__('beneficiary.section.history.labels.date')),
                TextColumn::make('time')
                    ->label(__('beneficiary.section.history.labels.time'))
                    ->state(fn (Activity $record): ?string => $record->created_at?->format('H:i')),
                TextColumn::make('causer.full_name')
                    ->label(__('beneficiary.section.history.labels.user')),
                TextColumn::make('description')
                    ->label(__('beneficiary.section.history.labels.description')),
                TextColumn::make('event')
                    ->label(__('beneficiary.section.history.labels.section'))
                    ->formatStateUsing(fn (Activity $record): string => ActivityLabelHelper::getEventLabel($record)),
                TextColumn::make('subsection')
                    ->label(__('beneficiary.section.history.labels.subsection'))
                    ->state(fn (Activity $record): string => ActivityLabelHelper::getSubsectionLabel($record)),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading(__('beneficiary.section.history.titles.list'))
            ->emptyStateIcon('heroicon-o-clock');
    }

    public static function canView(): bool
    {
        return true;
    }
}
