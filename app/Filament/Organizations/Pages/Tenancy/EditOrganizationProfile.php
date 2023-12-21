<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Pages\Tenancy;

use App\Filament\Admin\Resources\OrganizationResource;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\EditTenantProfile;

class EditOrganizationProfile extends EditTenantProfile
{
    protected static ?string $slug = 'organization';

    public static function getLabel(): string
    {
        return __('organization.profile');
    }

    public function form(Form $form): Form
    {
        return OrganizationResource::form($form);
    }
}
