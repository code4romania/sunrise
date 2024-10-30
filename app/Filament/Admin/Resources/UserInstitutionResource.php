<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserInstitutionResource\Pages;
use App\Models\User;
use Filament\Resources\Resource;

class UserInstitutionResource extends Resource
{
    protected static ?string $model = User::class;

    public static string $parentResource = InstitutionResource::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserInstitutions::route('/'),
        ];
    }
}
