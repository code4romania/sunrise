<?php

declare(strict_types=1);

namespace App\Filament\Resources\CommunityProfileResource\Pages;

use App\Filament\Resources\CommunityProfileResource;
use Filament\Resources\Pages\ListRecords;

class ListCommunityProfiles extends ListRecords
{
    protected static string $resource = CommunityProfileResource::class;
}
