<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Institutions\Resources\Users;

use App\Filament\Admin\Resources\Institutions\InstitutionResource;
use App\Filament\Admin\Resources\Institutions\Resources\Users\Pages\CreateUser;
use App\Filament\Admin\Resources\Institutions\Resources\Users\Pages\EditUser;
use App\Filament\Admin\Resources\Institutions\Resources\Users\Pages\ViewUser;
use App\Filament\Admin\Resources\Institutions\Resources\Users\Schemas\UserForm;
use App\Filament\Admin\Resources\Institutions\Resources\Users\Schemas\UserInfolist;
use App\Filament\Admin\Resources\Institutions\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\ParentResourceRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $recordTitleAttribute = 'first_name';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUser;

    protected static ?string $parentResource = InstitutionResource::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return InstitutionResource::asParent()
            ->relationship('admins')
            ->inverseRelationship('institution');
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UserInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
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
            'create' => CreateUser::route('/create'),
            'view' => ViewUser::route('/{record}'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
