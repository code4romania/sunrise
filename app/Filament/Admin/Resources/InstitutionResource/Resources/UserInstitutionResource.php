<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\InstitutionResource\Resources;

use App\Filament\Admin\Resources\InstitutionResource\Resources\UserInstitutionResource\Pages\ListUserInstitutions;
use App\Filament\Admin\Resources\InstitutionResource\Resources\UserInstitutionResource\Pages;
use App\Filament\Admin\Resources\InstitutionResource;
use App\Models\User;
use Filament\Resources\ParentResourceRegistration;
use Filament\Resources\Resource;

class UserInstitutionResource extends Resource
{
    protected static ?string $model = User::class;

    public static ?string $parentResource = InstitutionResource::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return InstitutionResource::asParent()
            ->relationship('admins')
            ->inverseRelationship('institution');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUserInstitutions::route('/'),
            'view' => Pages\ViewUserInstitution::route('/{record}'),
            'edit' => Pages\EditUserInstitution::route('/{record}/edit'),
        ];
    }
}
