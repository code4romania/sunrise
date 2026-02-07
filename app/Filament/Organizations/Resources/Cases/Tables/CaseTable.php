<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Tables;

use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Models\Beneficiary;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CaseTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query): Builder {
                return $query
                    ->with(['managerTeam.user', 'lastMonitoring'])
                    ->whereUserHasAccess();
            })
            ->columns(self::getColumns())
            ->headerActions([
                Action::make('register_case')
                    ->label(__('case.headings.register_new'))
                    ->url(CaseResource::getUrl('create'))
                    ->icon('heroicon-m-plus')
                    ->button(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label(__('general.action.view_details')),
            ])
            ->defaultSort('id', 'desc')
            ->paginationPageOptions([10, 20, 50])
            ->defaultPaginationPageOption(20)
            ->emptyStateHeading(__('case.empty_state.heading'))
            ->emptyStateDescription(__('case.empty_state.description'))
            ->emptyStateIcon('heroicon-o-clipboard-document-list')
            ->emptyStateActions([
                Action::make('register_first_case')
                    ->label(__('case.headings.register_first'))
                    ->url(CaseResource::getUrl('create'))
                    ->icon('heroicon-m-plus')
                    ->button(),
            ]);
    }

    /**
     * @return array<int, \Filament\Tables\Columns\TextColumn>
     */
    public static function getColumns(): array
    {
        return [
            TextColumn::make('id')
                ->label(__('case.table.file_number'))
                ->sortable()
                ->searchable(true, fn (Builder $query, string $search): Builder => $query->where('beneficiaries.id', 'like', '%'.$search.'%')),

            TextColumn::make('full_name')
                ->label(__('case.table.beneficiary'))
                ->description(fn (Beneficiary $record): string => $record->initial_id ? __('beneficiary.labels.reactivated') : '')
                ->sortable()
                ->searchable(true, fn (Builder $query, string $search): Builder => $query->where('beneficiaries.full_name', 'like', '%'.$search.'%')),

            TextColumn::make('created_at')
                ->label(__('case.table.opened_at'))
                ->date('d.m.Y')
                ->sortable(),

            TextColumn::make('lastMonitoring.date')
                ->label(__('case.table.monitored_at'))
                ->date('d.m.Y')
                ->placeholder('—'),

            TextColumn::make('managerTeam.user.full_name')
                ->label(__('case.table.case_manager'))
                ->formatStateUsing(fn (?string $state): string => collect(explode(',', (string) $state))->map(fn (string $item): string => trim($item))->unique()->filter()->join(', ') ?: '—'),

            TextColumn::make('status')
                ->label(__('case.table.status'))
                ->badge(),
        ];
    }
}
