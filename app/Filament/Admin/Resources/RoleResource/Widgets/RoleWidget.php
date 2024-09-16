<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\RoleResource\Widgets;

use App\Filament\Admin\Resources\RoleResource;
use App\Models\Role;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
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
                TextColumn::make('name'),
                TextColumn::make('institutions'),
                TextColumn::make('centers'),
                TextColumn::make('users_count'),
                TextColumn::make('status'),
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
            ->emptyStateHeading(__('nomenclature.labels.empty_state_role_table'))
            ->emptyStateDescription(null)
            ->emptyStateIcon('heroicon-o-clipboard-document-check');
    }
}
