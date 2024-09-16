<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\RoleResource\Widgets;

use App\Enums\GeneralStatus;
use App\Filament\Admin\Resources\RoleResource;
use App\Models\Role;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RoleWidget extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Role::query()
            )
            ->columns([
                TextColumn::make('name')
                    ->label(__('nomenclature.labels.role_name')),
                TextColumn::make('institutions')
                    ->label(__('nomenclature.labels.institutions')),
                TextColumn::make('centers')
                    ->label(__('nomenclature.labels.centers')),
                TextColumn::make('users_count')
                    ->label(__('nomenclature.labels.users')),
                TextColumn::make('status')
                    ->label(__('nomenclature.labels.status')),
            ])
            ->actions([
                ViewAction::make()
                    ->url(fn (Role $record) => RoleResource::getUrl('view', ['record' => $record])),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('nomenclature.actions.add_role'))
                    ->url(RoleResource::getUrl('create')),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(GeneralStatus::options()),
            ])
            ->heading(__('nomenclature.headings.roles_table'))
            ->emptyStateHeading(__('nomenclature.labels.empty_state_role_table'))
            ->emptyStateDescription(null)
            ->emptyStateIcon('heroicon-o-clipboard-document-check');
    }

    public function getDisplayName(): string
    {
        return __('nomenclature.headings.roles');
    }
}
