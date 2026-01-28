<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Widgets;

use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Models\Beneficiary;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RelatedCases extends BaseWidget
{
    public ?Model $record = null;

    protected string $view = 'widgets.related-cases-table';

    protected int | string | array $columnSpan = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Beneficiary::query()
                    ->with('managerTeam')
                    ->when(
                        $this->record?->initial_id,
                        fn (Builder $query) => $query
                            ->whereNot('id', $this->record?->id)
                            ->where('initial_id', $this->record->initial_id)
                            ->orWhere('id', $this->record->initial_id),
                        fn (Builder $query) => $query
                            ->where('initial_id', $this->record?->id)
                    )
            )
            ->columns([
                TextColumn::make('id')
                    ->label(__('field.case_id')),

                TextColumn::make('full_name')
                    ->label(__('field.beneficiary')),

                TextColumn::make('created_at')
                    ->label(__('field.open_at')),

                TextColumn::make('managerTeam.user.full_name')
                    ->label(__('beneficiary.section.related_cases.labels.case_manager'))
                    ->formatStateUsing(
                        fn ($state) => collect(explode(',', $state))
                            ->map(fn ($item) => trim($item))
                            ->unique()
                            ->join(', ')
                    ),

                TextColumn::make('status'),
            ])
            ->heading(__('beneficiary.labels.related_cases'))
            ->paginated(false)
            ->recordActions([
                \Filament\Actions\ViewAction::make()
                    ->label(__('general.action.view_details'))
                    ->color('primary')
                    ->url(fn (Beneficiary $record) => BeneficiaryResource::getUrl('view', ['record' => $record->id])),
            ])
            ->recordUrl(fn (Beneficiary $record) => BeneficiaryResource::getUrl('view', ['record' => $record->id]))
            ->defaultSort('id', 'desc');
    }
}
