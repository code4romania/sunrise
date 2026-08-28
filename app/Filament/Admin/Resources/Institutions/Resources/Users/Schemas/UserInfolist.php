<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Institutions\Resources\Users\Schemas;

use App\Filament\Admin\Resources\Institutions\Resources\Users\UserResource;
use App\Infolists\Components\Actions\EditAction;
use App\Models\User;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                    ->columnSpanFull()
                    ->schema([
                        Section::make(__('user.labels.account_status'))
                            ->schema([
                                Grid::make(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextEntry::make('userStatus.status')
                                            ->label(__('user.labels.account_status'))
                                            ->formatStateUsing(fn ($state, User $record) => $state ?? ($record->hasSetPassword() ? null : \App\Enums\UserStatus::PENDING))
                                            ->badge()
                                            ->placeholder('—'),
                                        TextEntry::make('last_login_at')
                                            ->label(__('user.labels.last_login_at_date_time'))
                                            ->formatStateUsing(function ($state) {
                                                if (blank($state) || $state === '-') {
                                                    return null;
                                                }
                                                try {
                                                    return \Carbon\Carbon::parse($state)->format('d.m.Y H:i');
                                                } catch (\Throwable) {
                                                    return null;
                                                }
                                            })
                                            ->placeholder('—'),
                                    ]),
                            ]),
                    ]),

                Section::make(__('user.heading.specialist_details'))
                    ->headerActions([
                        EditAction::make()
                            ->url(fn (User $record) => UserResource::getUrl('edit', [
                                'institution' => $record->institution,
                                'record' => $record,
                            ])),
                    ])
                    ->schema([
                        Grid::make(2)
                            ->columnSpanFull()
                            ->schema([
                                TextEntry::make('last_name')
                                    ->label(__('user.labels.first_name')),
                                TextEntry::make('first_name')
                                    ->label(__('user.labels.last_name')),
                                TextEntry::make('email')
                                    ->label(__('user.labels.email')),
                                TextEntry::make('phone_number')
                                    ->label(__('user.labels.phone_number')),
                            ]),
                    ]),
            ]);
    }
}
