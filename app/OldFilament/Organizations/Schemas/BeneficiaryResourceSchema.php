<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Schemas;

use App\Enums\CaseStatus;
use App\Filters\DateFilter;
use App\Tables\Filters\SelectFilter;
use Filament\Actions\ViewAction;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BeneficiaryResourceSchema
{
    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query
                    ->with([
                        'managerTeam',
                        'lastMonitoring',
                        // used for permissions
                        'specialistsMembers',
                    ])
                    ->whereUserHasAccess();
            })
            ->columns(self::getTableColumns())
            ->recordActions(self::getTableActions())
            ->filters(self::getTableFilters())
            ->paginationPageOptions([10, 20, 40, 60, 80, 100])
            ->defaultPaginationPageOption(20)
            ->defaultSort('id', 'desc');
    }

    public static function getTableColumns(): array
    {
        return [
            TextColumn::make('id')
                ->label(__('field.case_id'))
                ->sortable()
                ->searchable(true, fn (Builder $query, $search) => $query->where('beneficiaries.id', 'LIKE', '%'.$search.'%')),

            TextColumn::make('full_name')
                ->label(__('field.beneficiary'))
                ->description(fn ($record) => $record->initial_id ? __('beneficiary.labels.reactivated') : '')
                ->sortable()
                ->searchable(true, fn (Builder $query, $search) => $query->where('beneficiaries.full_name', 'LIKE', '%'.$search.'%')),

            TextColumn::make('created_at')
                ->label(__('field.open_at'))
                ->date()
                ->toggleable()
                ->sortable(),

            TextColumn::make('lastMonitoring.date')
                ->label(__('field.last_evaluated_at'))
                ->date()
                ->toggleable(),

            TextColumn::make('managerTeam.user.full_name')
                ->label(__('beneficiary.labels.case_manager'))
                ->toggleable()
                ->formatStateUsing(
                    fn ($state) => collect(explode(',', $state))
                        ->map(fn ($item) => trim($item))
                        ->unique()
                        ->join(', ')
                ),

            TextColumn::make('status')
                ->label(__('field.status'))
                ->badge(),
        ];
    }

    public static function getTableActions(): array
    {
        return [
            ViewAction::make()
                ->label(__('general.action.view_details')),
        ];
    }

    public static function getTableFilters(): array
    {
        return [
            SelectFilter::make('status')
                ->label(__('field.status'))
                ->options(CaseStatus::options())
                ->modifyQueryUsing(fn (Builder $query, $state) => $state['value'] ? $query->where('beneficiaries.status', $state) : $query),

            SelectFilter::make('case_manager')
                ->label(__('beneficiary.labels.case_manager'))
                ->searchable()
                ->preload()
                ->relationship('managerTeam.user', 'full_name'),

            DateFilter::make('created_at')
                ->label(__('field.open_at'))
                ->attribute('beneficiaries.created_at'),

            DateFilter::make('monitorings.date')
                ->label(__('field.last_evaluated_at'))
                ->modifyQueryUsing(
                    fn (Builder $query, array $state) => $query
                        ->when(data_get($state, 'date_from'), function (Builder $query, string $date) {
                            $query->whereHas('lastMonitoring', fn (Builder $query) => $query->whereDate('date', '>=', $date));
                        })
                        ->when(data_get($state, 'date_until'), function (Builder $query, string $date) {
                            $query->whereHas('lastMonitoring', fn (Builder $query) => $query->whereDate('date', '<=', $date));
                        })
                ),
        ];
    }
}
