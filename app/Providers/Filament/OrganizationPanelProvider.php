<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Pages;
use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Profile\UserPersonalInfo;
use App\Filament\Pages\Tenancy\EditOrganizationProfile;
use App\Http\Middleware\ApplyTenantScopes;
use App\Models\Organization;
use Filament\Forms\Components\DateTimePicker;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Tables\Table;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use Livewire\Livewire;

class OrganizationPanelProvider extends PanelProvider
{
    public static string $defaultDateDisplayFormat = 'd.m.Y';

    public static string $defaultDateTimeDisplayFormat = 'd.m.Y H:i';

    public static string $defaultDateTimeWithSecondsDisplayFormat = 'd.m.Y H:i:s';

    public static string $defaultTimeDisplayFormat = 'H:i';

    public static string $defaultTimeWithSecondsDisplayFormat = 'H:i:s';

    public function register(): void
    {
        parent::register();

        $this->setDefaultDateTimeDisplayFormats();
    }

    public function boot(): void
    {
        Livewire::component('user_personal_info', UserPersonalInfo::class);
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('organization')
            ->sidebarCollapsibleOnDesktop()
            ->collapsibleNavigationGroups(false)
            ->login(Login::class)
            ->colors([
                'primary' => Color::Purple,
            ])
            ->maxContentWidth('full')
            ->viteTheme('resources/css/filament/organization/theme.css')
            ->brandLogo(fn () => view('filament.brand'))
            ->brandLogoHeight('3rem')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                // Pages\Dashboard::class,
            ])
            ->discoverWidgets(
                in: app_path('Filament/Widgets/Organizations'),
                for: 'App\\Filament\\Widgets\\Organizations'
            )
            ->widgets([
                // Widgets\AccountWidget::class,
            ])
            ->bootUsing(function () {
                Page::alignFormActionsEnd();
            })
            ->databaseNotifications()
            ->plugins([
                BreezyCore::make()
                    ->myProfile(
                        hasAvatars: true,
                        slug: 'settings'
                    )
                    ->myProfileComponents([
                        'personal_info' => UserPersonalInfo::class,
                    ])
                    ->enableTwoFactorAuthentication(),
            ])
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
            ])
            ->tenant(Organization::class, 'slug')
            ->tenantProfile(EditOrganizationProfile::class)
            ->tenantMiddleware([
                // ApplyTenantScopes::class,
            ], isPersistent: true);
    }

    protected function setDefaultDateTimeDisplayFormats(): void
    {
        Table::$defaultDateDisplayFormat = static::$defaultDateDisplayFormat;
        Table::$defaultDateTimeDisplayFormat = static::$defaultDateTimeDisplayFormat;
        Table::$defaultTimeDisplayFormat = static::$defaultTimeDisplayFormat;

        Infolist::$defaultDateDisplayFormat = static::$defaultDateDisplayFormat;
        Infolist::$defaultDateTimeDisplayFormat = static::$defaultDateTimeDisplayFormat;
        Infolist::$defaultTimeDisplayFormat = static::$defaultTimeDisplayFormat;

        DateTimePicker::$defaultDateDisplayFormat = static::$defaultDateDisplayFormat;
        DateTimePicker::$defaultDateTimeDisplayFormat = static::$defaultDateTimeDisplayFormat;
        DateTimePicker::$defaultDateTimeWithSecondsDisplayFormat = static::$defaultDateTimeWithSecondsDisplayFormat;
        DateTimePicker::$defaultTimeDisplayFormat = static::$defaultTimeDisplayFormat;
        DateTimePicker::$defaultTimeWithSecondsDisplayFormat = static::$defaultTimeWithSecondsDisplayFormat;
    }
}
