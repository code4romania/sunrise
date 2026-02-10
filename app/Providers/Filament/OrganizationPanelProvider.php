<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Organizations\Pages\Tenancy\EditOrganizationProfile;
use App\Http\Middleware\EnsureUserIsActive;
use App\Livewire\Welcome;
use App\Models\Organization;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class OrganizationPanelProvider extends PanelProvider
{
    private const OrganizationsNamespace = 'App\\Filament\\Organizations';

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('organization')
            ->path('')
            ->login()
            ->tenant(Organization::class, slugAttribute: 'slug')
            ->tenantProfile(EditOrganizationProfile::class)
            ->colors([
                'primary' => Color::Violet,
            ])
            ->font('DM Sans')
            ->maxContentWidth('full')
            ->viteTheme('resources/css/filament/common/theme.css')
            ->brandLogo(fn () => view('filament.brand'))
            ->brandLogoHeight('3rem')
            ->darkMode(false)
            ->discoverResources(
                in: app_path('Filament/Organizations/Resources'),
                for: self::OrganizationsNamespace.'\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/Organizations/Pages'),
                for: self::OrganizationsNamespace.'\\Pages'
            )
            ->routes(function (): void {
                Route::get('/welcome/{user:ulid}', Welcome::class)->name('auth.welcome');
            })
            ->discoverWidgets(
                in: app_path('Filament/Organizations/Widgets'),
                for: self::OrganizationsNamespace.'\\Widgets'
            )
            ->widgets([
                AccountWidget::class,
            ])
            ->navigationGroups([
                __('navigation.beneficiaries._group'),
                __('navigation.configurations._group'),
            ])
            ->navigationItems([
                NavigationItem::make(__('navigation.configurations.organization'))
                    ->url(fn (): string => EditOrganizationProfile::getUrl(['tenant' => Filament::getTenant()]))
                    ->icon(Heroicon::OutlinedBuildingOffice)
                    ->group(__('navigation.configurations._group'))
                    ->visible(fn (): bool => auth()->user()?->hasAccessToOrganizationConfig() ?? false),
            ])
            ->unsavedChangesAlerts()
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                EnsureUserIsActive::class,
            ]);
    }
}
