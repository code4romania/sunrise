<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Pages\Tenancy;

use App\Filament\Admin\Resources\Institutions\Resources\Organizations\Schemas\OrganizationForm;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

use function Filament\authorize;

class EditOrganizationProfile extends EditTenantProfile
{
    protected static ?string $slug = 'organization';

    public static function getLabel(): string
    {
        return __('organization.profile');
    }

    public static function canView(Model $tenant): bool
    {
        $user = auth()->user();

        if (! $user || ! $tenant) {
            return false;
        }

        return $user->canChangeOrganizationProfile()
            && $user->organizations->contains($tenant);
    }

    public function mount(): void
    {
        $this->tenant = Filament::getTenant();

        if (! $this->tenant) {
            abort(404);
        }

        authorize('update', $this->tenant);

        $this->fillForm();
    }

    public function form(Schema $schema): Schema
    {
        return OrganizationForm::configure($schema);
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->before(function (): void {
                authorize('update', $this->tenant);
            });
    }
}
