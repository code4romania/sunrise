<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Staff\Schemas;

use App\Enums\AdminPermission;
use App\Enums\CasePermission;
use App\Forms\Components\Select;
use App\Models\OrganizationUserPermissions;
use App\Models\Role;
use App\Models\User;
use App\Rules\MultipleIn;
use Closure;
use Filament\Facades\Filament;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rules\Unique;

class StaffForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components(self::getFormComponents());
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    public static function getFormComponents(): array
    {
        return [
            Section::make()
                ->columns()
                ->maxWidth('3xl')
                ->visible(fn (string $operation) => $operation === 'edit')
                ->schema([
                    Placeholder::make('userStatus.status')
                        ->content(fn (User $record) => $record->userStatus?->status?->getLabel() ?? '-'),

                    Placeholder::make('last_login_at')
                        ->label(__('user.labels.last_login_at_date_time'))
                        ->content(
                            fn (User $record) => $record->last_login_at && $record->last_login_at !== '-'
                                ? (is_object($record->last_login_at) ? $record->last_login_at->format('d.m.Y H:i:s') : (string) $record->last_login_at)
                                : '-'
                        ),
                ]),

            Section::make()
                ->columns()
                ->maxWidth('3xl')
                ->schema([
                    TextInput::make('first_name')
                        ->label(__('user.labels.first_name'))
                        ->required(),

                    TextInput::make('last_name')
                        ->label(__('user.labels.last_name'))
                        ->required(),

                    TextInput::make('email')
                        ->label(__('user.labels.email'))
                        ->email()
                        ->unique(
                            ignoreRecord: true,
                            modifyRuleUsing: fn (Unique $rule, string $operation) => $operation === 'edit'
                                ? $rule
                                : $rule->whereIn('id', User::getTenantOrganizationUsers()->keys())
                        )
                        ->required(),

                    TextInput::make('phone_number')
                        ->label(__('user.labels.phone_number'))
                        ->tel()
                        ->maxLength(14)
                        ->required(),

                    Select::make('role_id')
                        ->label(__('user.labels.select_roles'))
                        ->options(Role::active()->pluck('name', 'id'))
                        ->preload()
                        ->multiple()
                        ->live()
                        ->required()
                        ->rules([
                            'array',
                            'min:1',
                            new MultipleIn(Role::active()->pluck('id')->toArray()),
                        ])
                        ->afterStateHydrated(self::setDefaultCaseAndNgoAdminPermissions())
                        ->afterStateUpdated(self::setDefaultCaseAndNgoAdminPermissions()),

                    Placeholder::make('obs')
                        ->hiddenLabel()
                        ->visible(fn (Get $get) => $get('role_id'))
                        ->content(function (Get $get) {
                            foreach ($get('role_id') ?? [] as $roleID) {
                                $role = self::getRole($roleID);
                                if ($role && $role->case_permissions->contains(CasePermission::HAS_ACCESS_TO_ALL_CASES)) {
                                    return new HtmlString(__('user.placeholders.user_role_with_permissions_for_all_cases'));
                                }
                            }

                            return new HtmlString(__('user.placeholders.user_role_without_permissions_for_all_cases'));
                        })
                        ->columnSpanFull(),

                    Group::make()
                        ->relationship('permissions')
                        ->columnSpanFull()
                        ->schema([
                            CheckboxList::make('case_permissions')
                                ->label(__('user.labels.case_permissions'))
                                ->options(CasePermission::getOptionsWithoutCaseManager())
                                ->in(array_keys(CasePermission::getOptionsWithoutCaseManager()))
                                ->disableOptionWhen(function (Get $get, string $value, ?OrganizationUserPermissions $record) {
                                    if ($record?->user->isNgoAdmin()) {
                                        return true;
                                    }

                                    foreach ($get('../role_id') ?? [] as $roleID) {
                                        $role = self::getRole($roleID);
                                        if (! $role) {
                                            continue;
                                        }

                                        $permission = $role->case_permissions
                                            ->filter(fn ($item) => CasePermission::isValue($value, $item));
                                        if ($permission->count() > 0) {
                                            return true;
                                        }
                                    }

                                    return false;
                                })
                                ->columnSpanFull(),

                            CheckboxList::make('admin_permissions')
                                ->label(__('user.labels.admin_permissions'))
                                ->options(AdminPermission::options())
                                ->in(array_keys(AdminPermission::options()))
                                ->disableOptionWhen(function (Get $get, string $value, ?OrganizationUserPermissions $record) {
                                    if ($record?->user->isNgoAdmin()) {
                                        return true;
                                    }

                                    foreach ($get('../role_id') ?? [] as $roleID) {
                                        $role = self::getRole($roleID);
                                        if (! $role) {
                                            continue;
                                        }

                                        $permission = $role->ngo_admin_permissions
                                            ->filter(fn ($item) => AdminPermission::isValue($value, $item));
                                        if ($permission->count() > 0) {
                                            return true;
                                        }
                                    }

                                    return false;
                                })
                                ->columnSpanFull(),

                            Hidden::make('organization_id')
                                ->default(fn () => Filament::getTenant()?->id),
                        ]),
                ]),
        ];
    }

    /**
     * @return Closure(Set, Get, mixed, User|null): void
     */
    public static function setDefaultCaseAndNgoAdminPermissions(): Closure
    {
        return function (Set $set, Get $get, $state, ?User $record) {
            $casePermissions = $get('permissions.case_permissions') ?: [];
            $adminPermissions = $get('permissions.admin_permissions') ?: [];

            if ($record?->isNgoAdmin()) {
                $set('permissions.case_permissions', CasePermission::values());
                $set('permissions.admin_permissions', AdminPermission::values());

                return;
            }

            foreach ((array) $state as $roleID) {
                $role = self::getRole($roleID);
                if (! $role) {
                    continue;
                }

                $defaultCasePermissions = $role->case_permissions?->map(fn ($item) => $item->value)->toArray() ?? [];
                $defaultNgoAdminPermissions = $role->ngo_admin_permissions?->map(fn ($item) => $item->value)->toArray() ?? [];

                $casePermissions = array_merge($casePermissions, $defaultCasePermissions);
                $adminPermissions = array_merge($adminPermissions, $defaultNgoAdminPermissions);
            }
            $casePermissions = array_values(array_unique($casePermissions));
            $adminPermissions = array_values(array_unique($adminPermissions));
            sort($casePermissions);
            sort($adminPermissions);

            $set('permissions.case_permissions', $casePermissions);
            $set('permissions.admin_permissions', $adminPermissions);
        };
    }

    protected static function getRole(mixed $roleID): ?Role
    {
        return Cache::driver('array')
            ->rememberForever(
                'role_'.$roleID,
                fn () => Role::find($roleID)
            );
    }
}
