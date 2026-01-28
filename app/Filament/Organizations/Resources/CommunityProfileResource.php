<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources;

use App\Filament\Organizations\Resources\CommunityProfileResource\Pages\EditCommunityProfile;
use App\Filament\Organizations\Schemas\CommunityProfileResourceSchema;
use App\Models\CommunityProfile;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;

class CommunityProfileResource extends Resource
{
    protected static ?string $model = CommunityProfile::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-check-badge';

    protected static bool $isScopedToTenant = false;

    protected static ?string $slug = 'community-profile';

    protected static ?int $navigationSort = 21;

    public static function canAccess(): bool
    {
        return auth()->user()->canChangeOrganizationProfile();
    }

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
            'index' => EditCommunityProfile::route('/'),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return CommunityProfileResourceSchema::form($schema);
    }
}
