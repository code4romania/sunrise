<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\UserResource\Pages;

use App\Enums\AdminPermission;
use App\Enums\CasePermission;
use App\Enums\Ternary;
use App\Filament\Organizations\Resources\UserResource;
use App\Infolists\Components\SectionHeader;
use App\Models\User;
use Filament\Actions;
use Filament\Infolists\Components\Group;
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
                        ->formatStateUsing(fn ($state) => $state === '-' ? $state : $state->label()),
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

                    TextEntry::make('rolesInOrganization.name')
                        ->label(__('user.labels.select_roles'))
                        ->columnSpanFull(),

                    TextEntry::make('obs')
                        ->default(
                            Str::of(__('user.placeholders.obs'))
                                ->inlineMarkdown()
                                ->toHtmlString()
                        )
                        ->hiddenLabel()
                        ->columnSpanFull(),

                    Group::make()
                        ->columnSpanFull()
                        ->schema(function (User $record) {
                            $fields = [];
                            $fields[] = SectionHeader::make('case_permissions_group')
                                ->state(__('user.labels.case_permissions'));
                            foreach (CasePermission::options() as $key => $option) {
                                $fields[] = TextEntry::make($key)
                                    ->label($option)
                                    ->state($record->case_permissions && \in_array($key, $record->case_permissions) ?
                                        Ternary::YES : Ternary::NO);
                            }

                            return $fields;
                        }),

                    Group::make()
                        ->columnSpanFull()
                        ->schema(function (User $record) {
                            $fields = [];
                            $fields[] = SectionHeader::make('admin_permissions')
                                ->state(__('user.labels.admin_permissions'));
                            foreach (AdminPermission::options() as $key => $option) {
                                $fields[] = TextEntry::make($key)
                                    ->label($option)
                                    ->state($record->admin_permissions && \in_array($key, $record->admin_permissions) ?
                                        Ternary::YES : Ternary::NO);
                            }

                            return $fields;
                        }),
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
