<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\OrganizationResource\Pages;

use App\Concerns\PreventMultipleSubmit;
use App\Concerns\PreventSubmitFormOnEnter;
use App\Filament\Admin\Resources\OrganizationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrganization extends CreateRecord
{
    use PreventMultipleSubmit;
    use PreventSubmitFormOnEnter;

    protected static string $resource = OrganizationResource::class;

    protected static bool $canCreateAnother = false;
}
