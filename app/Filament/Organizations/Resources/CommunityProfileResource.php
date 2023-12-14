<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources;

use App\Filament\Organizations\Resources\CommunityProfileResource\Pages;
use App\Models\CommunityProfile;
use Filament\Resources\Resource;

class CommunityProfileResource extends Resource
{
    protected static ?string $model = CommunityProfile::class;

    protected static ?string $navigationIcon = 'heroicon-o-at-symbol';

    protected static bool $isScopedToTenant = false;

    protected static ?string $slug = 'community-profile';

    protected static ?int $navigationSort = 21;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.community._group');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.community.profile');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\EditCommunityProfile::route('/'),
        ];
    }
}
