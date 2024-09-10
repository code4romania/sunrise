<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources;

use App\Enums\AdminPermission;
use App\Enums\CasePermission;
use App\Enums\Role;
use App\Filament\Organizations\Resources\UserResource\Pages;
use App\Forms\Components\Select;
use App\Models\User;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

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
            ->columns([
                TextColumn::make('first_name')
                    ->sortable()
                    ->label(__('user.labels.first_name'))
                    ->searchable(),
                TextColumn::make('last_name')
                    ->label(__('user.labels.last_name'))
                    ->searchable(),
                TextColumn::make('roles')
                    ->sortable()
                    ->badge()
                    ->label(__('user.labels.roles'))
                    ->formatStateUsing(fn ($state) => $state->label()),
                TextColumn::make('status')
                    ->sortable()
                    ->label(__('user.labels.account_status'))
                    ->formatStateUsing(fn ($state) => $state->label()),
                TextColumn::make('last_login_at')
                    ->sortable()
                    ->label(__('user.labels.last_login_at')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->heading(__('user.heading.table'));
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
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
                        ->required(),
                    TextInput::make('phone_number')
                        ->label(__('user.labels.phone_number'))
                        ->tel()
                        ->required(),
                    Select::make('roles')
                        ->label(__('user.labels.select_roles'))
                        ->options(Role::options())
                        ->multiple()
                        ->required(),
                    Checkbox::make('can_be_case_manager')
                        ->label(__('user.labels.can_be_case_manager')),
                    Placeholder::make('obs')
                        ->content(__('user.placeholders.obs'))
                        ->label('')
                        ->columnSpanFull(),
                    CheckboxList::make('case_permissions')
                        ->label(__('user.labels.case_permissions'))
                        ->options(CasePermission::options())
                        ->columnSpanFull(),
                    CheckboxList::make('admin_permissions')
                        ->label(__('user.labels.admin_permissions'))
                        ->options(AdminPermission::options())
                        ->columnSpanFull(),
                ]),
        ];
    }
}
