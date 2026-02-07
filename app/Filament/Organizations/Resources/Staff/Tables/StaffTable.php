<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Staff\Tables;

use App\Models\User;
use App\Tables\Columns\DateTimeColumn;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StaffTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['rolesInOrganization', 'userStatus']))
            ->heading(__('user.heading.table'))
            ->columns([
                TextColumn::make('first_name')
                    ->sortable()
                    ->label(__('user.labels.first_name'))
                    ->searchable(),

                TextColumn::make('last_name')
                    ->label(__('user.labels.last_name'))
                    ->searchable(),

                TextColumn::make('rolesInOrganization.name')
                    ->sortable()
                    ->label(__('user.labels.roles')),

                TextColumn::make('userStatus.status')
                    ->sortable()
                    ->label(__('user.labels.account_status'))
                    ->suffix(fn (User $record) => $record->isNgoAdmin() ? '**' : ''),

                DateTimeColumn::make('last_login_at')
                    ->sortable()
                    ->label(__('user.labels.last_login_at')),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label(__('general.action.view_details')),
            ]);
    }
}
