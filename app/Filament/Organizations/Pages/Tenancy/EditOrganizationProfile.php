<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Pages\Tenancy;

use App\Filament\Admin\Resources\OrganizationResource;
use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Schemas\Schema;

class EditOrganizationProfile extends EditTenantProfile
{
    protected static ?string $slug = 'organization';

    public static function getLabel(): string
    {
        return __('organization.profile');
    }

    public function form(Schema $schema): Schema
    {
        return OrganizationResource::form($schema);
    }
}
