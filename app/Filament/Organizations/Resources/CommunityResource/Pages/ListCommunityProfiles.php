<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\CommunityResource\Pages;

use App\Filament\Organizations\Resources\CommunityResource;
use Filament\Resources\Pages\ListRecords;

class ListCommunityProfiles extends ListRecords
{
    protected static string $resource = CommunityResource::class;
}
