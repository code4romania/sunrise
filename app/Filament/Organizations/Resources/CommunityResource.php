<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources;

use App\Filament\Organizations\Resources\CommunityResource\Pages\ListCommunityProfiles;
use App\Filament\Organizations\Resources\CommunityResource\Pages\ViewCommunityProfile;
use App\Filament\Organizations\Schemas\CommunityResourceSchema;
use App\Models\CommunityProfile;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CommunityResource extends Resource
{
    protected static ?string $model = CommunityProfile::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-sun';

    protected static ?string $recordRouteKeyName = 'slug';

    protected static bool $isScopedToTenant = false;

    protected static ?string $slug = 'community';

    protected static ?int $navigationSort = 20;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.community._group');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.community.network');
    }

    public static function infolist(Schema $schema): Schema
    {
        return CommunityResourceSchema::infolist($schema);
    }

    public static function table(Table $table): Table
    {
        return CommunityResourceSchema::table($table)
            ->contentGrid([
                'default' => 1,
            ]);
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
            'index' => ListCommunityProfiles::route('/'),
            'view' => ViewCommunityProfile::route('/{record:slug}'),
        ];
    }
}
