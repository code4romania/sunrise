<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\CommunityResource\Pages;

use App\Filament\Organizations\Resources\CommunityResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListCommunityProfiles extends ListRecords
{
    protected static string $resource = CommunityResource::class;

    protected static string $view = 'filament.organizations.pages.empty-page';

    public function getTitle(): string|Htmlable
    {
        return __('community.headings.empty_state_title');
    }
}
