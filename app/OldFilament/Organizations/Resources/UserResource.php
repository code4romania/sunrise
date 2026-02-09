<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources;

use App\Filament\Organizations\Resources\UserResource\Pages\CreateUser;
use App\Filament\Organizations\Resources\UserResource\Pages\EditUser;
use App\Filament\Organizations\Resources\UserResource\Pages\ListUsers;
use App\Filament\Organizations\Resources\UserResource\Pages\ViewUser;
use App\Filament\Organizations\Schemas\UserResourceSchema;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

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

    public static function form(Schema $schema): Schema
    {
        return UserResourceSchema::form($schema);
    }

    public static function table(Table $table): Table
    {
        return UserResourceSchema::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view' => ViewUser::route('/{record}'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
