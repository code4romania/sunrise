<?php

namespace App\Filament\Admin\Resources\Institutions\Resources\Organizations\Pages;

use App\Filament\Admin\Resources\Institutions\Resources\Organizations\OrganizationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewOrganization extends ViewRecord
{
    protected static string $resource = OrganizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
