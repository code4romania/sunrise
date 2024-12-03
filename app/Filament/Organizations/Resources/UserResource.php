<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources;

use App\Enums\AdminPermission;
use App\Enums\CasePermission;
use App\Filament\Organizations\Resources\UserResource\Pages;
use App\Forms\Components\Select;
use App\Models\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rules\Unique;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $tenantOwnershipRelationshipName = 'organizations';

    protected static ?int $navigationSort = 31;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.configurations._group');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.configurations.staff');
    }

    public static function getModelLabel(): string
    {
        return __('user.specialist_label.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('user.specialist_label.plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(self::getSchema());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['rolesInOrganization', 'userStatus']))
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
                    ->label(__('user.labels.account_status')),

                TextColumn::make('last_login_at')
                    ->sortable()
                    ->label(__('user.labels.last_login_at')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(__('general.action.view_details')),
            ])
            ->heading(__('user.heading.table'));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    /**
     * @return array
     */
    public static function getSchema(): array
    {
        return [
            Section::make()
                ->columns()
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
                            modifyRuleUsing: fn (Unique $rule) => $rule->whereIn('id', User::getTenantOrganizationUsers()->keys())
                        )
                        ->required(),

                    TextInput::make('phone_number')
                        ->label(__('user.labels.phone_number'))
                        ->tel()
                        ->maxLength(14)
                        ->required(),

                    Select::make('role_id')
                        ->label(__('user.labels.select_roles'))
                        ->relationship('rolesInOrganization', 'name')
                        ->options(Role::active()->pluck('name', 'id'))
                        ->preload()
                        ->multiple()
                        ->live()
                        ->required()
                        ->afterStateHydrated(self::setDefaultCaseAndNgoAdminPermissions())
                        ->afterStateUpdated(self::setDefaultCaseAndNgoAdminPermissions()),

                    Placeholder::make('obs')
                        ->content(new HtmlString(__('user.placeholders.obs')))
                        ->label('')
                        ->columnSpanFull(),

                    Group::make()
                        ->relationship('permissions')
                        ->schema([
                            CheckboxList::make('case_permissions')
                                ->label(__('user.labels.case_permissions'))
                                ->options(CasePermission::getOptionsWithoutCaseManager())
                                ->disableOptionWhen(function (Get $get, string $value) {
                                    foreach ($get('../role_id') as $roleID) {
                                        $role = self::getRole($roleID);

                                        $permission = $role->case_permissions
                                            ->filter(fn ($item) => CasePermission::isValue($value, $item));
                                        if ($permission->count()) {
                                            return true;
                                        }
                                    }

                                    return false;
                                })
                                ->columnSpanFull(),

                            CheckboxList::make('admin_permissions')
                                ->label(__('user.labels.admin_permissions'))
                                ->options(AdminPermission::options())
                                ->disableOptionWhen(function (Get $get, string $value) {
                                    foreach ($get('../role_id') as $roleID) {
                                        $role = self::getRole($roleID);

                                        $permission = $role->ngo_admin_permissions
                                            ->filter(fn ($item) => AdminPermission::isValue($value, $item));
                                        if ($permission->count()) {
                                            return true;
                                        }
                                    }

                                    return false;
                                })
                                ->columnSpanFull(),

                            Hidden::make('organization_id')
                                ->default(Filament::getTenant()->id),
                        ]),
                ]),
        ];
    }

    public static function setDefaultCaseAndNgoAdminPermissions(): \Closure
    {
        return function (Set $set, Get $get, $state) {
            $casePermissions = $get('permissions.case_permissions') ?: [];
            $adminPermissions = $get('permissions.admin_permissions') ?: [];
            foreach ($state as $roleID) {
                $role = self::getRole($roleID);
                $defaultCasePermissions = $role->case_permissions?->map(fn ($item) => $item->value)->toArray();
                $defaultNgoAdminPermissions = $role->ngo_admin_permissions?->map(fn ($item) => $item->value)->toArray();

                $casePermissions = array_merge($casePermissions, $defaultCasePermissions);
                $adminPermissions = array_merge($adminPermissions, $defaultNgoAdminPermissions);
            }
            $casePermissions = array_unique($casePermissions);
            $adminPermissions = array_unique($adminPermissions);
            sort($casePermissions);
            sort($adminPermissions);

            $set('permissions.case_permissions', $casePermissions);
            $set('permissions.admin_permissions', $adminPermissions);
        };
    }

    protected static function getRole(mixed $roleID)
    {
        return Cache::driver('array')
            ->rememberForever(
                'role_' . $roleID,
                fn () => Role::find($roleID)
            );
    }
}
