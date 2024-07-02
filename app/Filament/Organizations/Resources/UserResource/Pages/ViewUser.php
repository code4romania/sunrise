<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\UserResource\Pages;

use App\Filament\Organizations\Resources\UserResource;
use Filament\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Str;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make()
                ->columns()
                ->schema([
                    TextEntry::make('status')
                        ->formatStateUsing(fn ($state) => $state->label()),
                    TextEntry::make('updated_at'),
                ]),
            Section::make()
                ->columns()
                ->schema([
                    TextEntry::make('first_name')
                        ->label(__('user.labels.first_name')),
                    TextEntry::make('last_name')
                        ->label(__('user.labels.last_name')),
                    TextEntry::make('email')
                        ->label(__('user.labels.email')),
                    TextEntry::make('phone_number')
                        ->label(__('user.labels.phone_number')),
                    TextEntry::make('roles')
                        ->label(__('user.labels.select_roles'))
                        ->badge(fn ($state) => $state != '-')
                        ->formatStateUsing(fn ($state) => $state != '-' ? $state->label() : $state),
                    TextEntry::make('can_be_case_manager')
                        ->label(__('user.labels.can_be_case_manager'))
                        ->default('0')
                        ->formatStateUsing(fn ($state) => $state != '-' ? __('enum.ternary.' . $state) : $state),
                    TextEntry::make('obs')
                        ->default(
                            Str::of(__('user.placeholders.obs'))
                                ->inlineMarkdown()
                                ->toHtmlString()
                        )
                        ->hiddenLabel()
                        ->columnSpanFull(),
                    TextEntry::make('case_permissions')
                        ->label(__('user.labels.case_permissions'))
                        ->badge(fn ($state) => $state != '-')
                        ->formatStateUsing(fn ($state) => $state != '-' ? __('enum.case_permissions.' . $state) : $state)
                        ->columnSpanFull(),
                    TextEntry::make('admin_permissions')
                        ->label(__('user.labels.admin_permissions'))
                        ->badge(fn ($state) => $state != '-')
                        ->formatStateUsing(fn ($state) => $state != '-' ? __('enum.admin_permission.' . $state) : $state)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make('edit'),

            UserResource\Actions\DeactivateUserAction::make(),

            //            UserResource\Actions\ResetPassword::make('reset-password'),

            UserResource\Actions\ResendInvitationAction::make(),
        ];
    }

    public function getBreadcrumb(): string
    {
        return $this->getTitle();
    }

    public function getTitle(): string
    {
        return $this->record->getFilamentName();
    }
}
