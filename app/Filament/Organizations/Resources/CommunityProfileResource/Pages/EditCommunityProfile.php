<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\CommunityProfileResource\Pages;

use App\Filament\Organizations\Resources\CommunityProfileResource;
use Filament\Resources\Pages\EditRecord;

class EditCommunityProfile extends EditRecord
{
    protected static string $resource = CommunityProfileResource::class;

    public function mount($record = null): void
    {
        $this->record = filament()->getTenant()->communityProfile;

        $this->authorizeAccess();

        $this->fillForm();

        $this->previousUrl = url()->previous();
    }
}
