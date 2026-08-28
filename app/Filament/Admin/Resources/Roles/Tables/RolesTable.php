<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Roles\Tables;

use App\Filament\Admin\Resources\Roles\RoleResource;
use App\Models\Role;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount(['users'])->with(['organizations'])->orderBy('sort'))
            ->columns([
                TextColumn::make('name')
                    ->label(__('nomenclature.labels.role_name'))
                    ->searchable(),
                TextColumn::make('institutions_count')
                    ->label(__('nomenclature.labels.institutions'))
                    ->state(fn (Role $record): int => $record->organizations->unique('institution_id')->count()),
                TextColumn::make('centers_count')
                    ->label(__('nomenclature.labels.centers'))
                    ->state(fn (Role $record): int => $record->organizations->count()),
                TextColumn::make('users_count')
                    ->label(__('nomenclature.labels.users')),
                TextColumn::make('status')
                    ->label(__('nomenclature.labels.status'))
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(\App\Enums\GeneralStatus::options()),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn (Role $record) => RoleResource::getUrl('view', ['record' => $record])),
                EditAction::make()
                    ->url(fn (Role $record) => RoleResource::getUrl('edit', ['record' => $record])),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('nomenclature.actions.add_role'))
                    ->url(RoleResource::getUrl('create')),
            ])
            ->heading(__('nomenclature.headings.roles_table'))
            ->emptyStateHeading(__('nomenclature.labels.empty_state_role_table'))
            ->emptyStateIcon('heroicon-o-clipboard-document-check');
    }
}
