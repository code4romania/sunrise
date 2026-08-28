<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Institutions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InstitutionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading(__('institution.headings.all_institutions'))
            ->emptyStateHeading(__('institution.headings.empty_state'))
            ->emptyStateIcon('heroicon-o-clipboard-document-list')
            ->modifyQueryUsing(
                fn (Builder $query) => $query
                    ->withCount(['organizations', 'beneficiaries', 'users'])
                    ->with(['county', 'city'])
            )
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label(__('institution.headings.institution_name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('county_and_city')
                    ->label(__('institution.headings.registered_office'))
                    ->state(fn ($record) => $record->county_and_city),

                TextColumn::make('organizations_count')
                    ->label(__('institution.headings.centers')),

                TextColumn::make('beneficiaries_count')
                    ->label(__('institution.headings.cases')),

                TextColumn::make('users_count')
                    ->label(__('institution.headings.specialists')),

                TextColumn::make('status')
                    ->label(__('institution.headings.status'))
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->label(__('general.action.view_details')),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
