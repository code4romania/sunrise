<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\UserResource\Pages;

use App\Enums\AdminPermission;
use App\Enums\CasePermission;
use App\Enums\Ternary;
use App\Filament\Organizations\Resources\UserResource;
use App\Infolists\Components\SectionHeader;
use App\Models\User;
use Filament\Infolists\Components\Actions\Action;
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
                ->maxWidth('3xl')
                ->schema([
                    TextEntry::make('status')
                        ->formatStateUsing(fn ($state) => $state === '-' ? $state : $state->label()),
                    TextEntry::make('last_login_at')
                        ->label(__('user.labels.last_login_at_date_time')),
                ]),
            Section::make(__('user.heading.specialist_details'))
                ->columns()
                ->maxWidth('3xl')
                ->headerActions([
                    Action::make('edit')
                        ->label(__('general.action.edit'))
                        ->url(self::$resource::getUrl('edit', ['record' => $this->getRecord()]))
                        ->link(),

                ])
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
                            foreach (CasePermission::cases() as $option) {
                                $fields[] = TextEntry::make($option->value)
                                    ->label($option->getLabel())
                                    ->state($record->permissions?->case_permissions->contains($option) ?
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
                            foreach (AdminPermission::cases() as $option) {
                                $fields[] = TextEntry::make($option->value)
                                    ->label($option->getLabel())
                                    ->state($record->permissions?->admin_permissions->contains($option) ?
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
