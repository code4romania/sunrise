<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Widgets;

use App\Filament\Organizations\Pages\Dashboard;
use App\Filament\Organizations\Resources\CommunityProfileResource;
use App\Filament\Organizations\Resources\ServiceResource;
use App\Filament\Organizations\Resources\UserResource;
use App\Infolists\Components\SectionHeader;
use App\Models\OrganizationService;
use App\Models\User;
use App\Widgets\InfolistWidget;
use Filament\Facades\Filament;
use Filament\Infolists\Components\Actions\Action;
use Illuminate\Database\Eloquent\Model;

class ConfigProgress extends InfolistWidget
{
    protected static string $view = 'filament.organizations.widgets.config-progress';

    protected Model | null $record = null;

    protected int | string | array $columnSpan = 2;

    protected static bool | null $organizationServices = null;

    protected static bool | null $specialistsStatus = null;

    public static function canView(): bool
    {
        if (! auth()->user()->isNgoAdmin() || auth()->user()->config_process) {
            return false;
        }

        return true;
    }

    public function getProgressData(): array
    {
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
                'link' => UserResource::getUrl(),
                'completed' => self::getSpecialistsStatus(),
            ],
            [
                'label' => __('dashboard.labels.config_organization_profile'),
                'link' => CommunityProfileResource::getUrl(),
                // TODO change this after implement community profile
                'completed' => false,
            ],
        ];
    }

    public function getInfolistSchema(): array
    {
        return [
            SectionHeader::make('header')
                ->state(fn () => $this->getWidgetTitle())
                ->action(
                    Action::make('close_action')
                        ->icon('heroicon-o-x-mark')
                        ->iconButton()
                        ->url(Dashboard::getUrl(['close_config_progress' => true]))
                        // TODO add community profile
                        ->visible(fn () => self::getOrganizationServiceStatus() && self::getSpecialistsStatus())
                ),
        ];
    }

    public function getWidgetTitle(): string
    {
        return __('dashboard.headings.config_progress');
    }

    protected static function getOrganizationServiceStatus(): bool
    {
        if (self::$organizationServices !== null) {
            return self::$organizationServices;
        }

        self::$organizationServices = (bool) OrganizationService::count();

        return self::$organizationServices;
    }

    public function getSpecialistsStatus(): bool
    {
        if (self::$specialistsStatus !== null) {
            return self::$specialistsStatus;
        }

        self::$specialistsStatus = (bool) Filament::getTenant()
            ->users
            ->filter(fn (User $user) => ! $user->isNgoAdmin())
            ->count();

        return self::$specialistsStatus;
    }
}
