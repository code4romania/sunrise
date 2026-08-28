<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Widgets;

use App\Filament\Organizations\Pages\Dashboard;
use App\Filament\Organizations\Pages\Tenancy\EditOrganizationProfile;
use App\Filament\Organizations\Resources\Services\ServiceResource;
use App\Filament\Organizations\Resources\Staff\StaffResource;
use App\Models\OrganizationService;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;

class ConfigProgressWidget extends Widget
{
    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 1;

    protected static ?bool $organizationServices = null;

    protected static ?bool $specialistsStatus = null;

    protected static ?string $heading = null;

    public static function canView(): bool
    {
        $user = auth()->user();

        if (! $user?->isNgoAdmin() || $user->config_process) {
            return false;
        }

        return true;
    }

    public function getHeading(): string
    {
        return $this->isFullyCompleted()
            ? __('dashboard.headings.config_progress_completed')
            : __('dashboard.headings.config_progress');
    }

    public function isFullyCompleted(): bool
    {
        return self::getOrganizationServiceStatus() && self::getSpecialistsStatus();
    }

    /**
     * @return array<int, array{label: string, link: string, completed: bool}>
     */
    public function getProgressData(): array
    {
        $tenant = Filament::getTenant();

        return [
            [
                'label' => __('dashboard.labels.accept_invitation'),
                'link' => Dashboard::getUrl(),
                'completed' => true,
            ],
            [
                'label' => __('dashboard.labels.config_services_nomenclature'),
                'link' => ServiceResource::getUrl(),
                'completed' => self::getOrganizationServiceStatus(),
            ],
            [
                'label' => __('dashboard.labels.config_specialists'),
                'link' => StaffResource::getUrl(),
                'completed' => self::getSpecialistsStatus(),
            ],
            [
                'label' => __('dashboard.labels.config_organization_profile'),
                'link' => EditOrganizationProfile::getUrl(['tenant' => $tenant]),
                'completed' => false,
            ],
        ];
    }

    public function getCloseUrl(): string
    {
        return Dashboard::getUrl(['close_config_progress' => true]);
    }

    public function canClose(): bool
    {
        return self::getOrganizationServiceStatus() && self::getSpecialistsStatus();
    }

    protected static function getOrganizationServiceStatus(): bool
    {
        if (self::$organizationServices !== null) {
            return self::$organizationServices;
        }

        self::$organizationServices = OrganizationService::query()->count() > 0;

        return self::$organizationServices;
    }

    protected static function getSpecialistsStatus(): bool
    {
        if (self::$specialistsStatus !== null) {
            return self::$specialistsStatus;
        }

        $tenant = Filament::getTenant();
        self::$specialistsStatus = $tenant
            ->users
            ->filter(fn (User $user): bool => ! $user->isNgoAdmin())
            ->count() > 0;

        return self::$specialistsStatus;
    }

    public function render(): View
    {
        return view('filament.organizations.widgets.config-progress');
    }
}
