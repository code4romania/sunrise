<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Organizations\Pages;
use App\Filament\Organizations\Pages\Profile\UserPersonalInfo;
use App\Http\Middleware\UpdateDefaultTenant;
use App\Livewire\Welcome;
use App\Models\Organization;
use Filament\Actions\MountableAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Infolists\Infolist;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Table;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
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
            ->login(Pages\Auth\Login::class)
            ->passwordReset()
            ->colors([
                'primary' => Color::Purple,
            ])
            ->maxContentWidth('full')
            ->viteTheme('resources/css/filament/common/theme.css')
            ->brandLogo(fn () => view('filament.brand'))
            ->brandLogoHeight('3rem')
            ->darkMode(false)
            ->discoverResources(
                in: app_path('Filament/Organizations/Resources'),
                for: 'App\\Filament\\Organizations\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/Organizations/Pages'),
                for: 'App\\Filament\\Organizations\\Pages'
            )
            ->pages([
                Pages\Dashboard::class,
            ])
            ->routes(function () {
                Route::get('/welcome/{user:ulid}', Welcome::class)->name('auth.welcome');
            })
            ->discoverWidgets(
                in: app_path('Filament/Organizations/Widgets'),
                for: 'App\\Filament\\Organizations\\Widgets'
            )
            ->widgets([
                // Widgets\AccountWidget::class,
            ])
            ->bootUsing(function () {
                Page::stickyFormActions();
                Page::alignFormActionsEnd();
                MountableAction::configureUsing(function (MountableAction $action) {
                    $action->modalFooterActionsAlignment(Alignment::Right);
                });
            })
            ->unsavedChangesAlerts()
            // ->databaseNotifications()
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
            ->navigationItems([
                NavigationItem::make(__('navigation.configurations.organization'))
                    ->group(__('navigation.configurations._group'))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->url(fn () => Filament::getTenantProfileUrl())
                    ->isActiveWhen(
                        fn () => url()->current() === Filament::getTenantProfileUrl()
                    )
                    ->sort(30),
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
            ->tenantProfile(Pages\Tenancy\EditOrganizationProfile::class)
            ->tenantRoutePrefix('org')
            ->tenantMiddleware([
                UpdateDefaultTenant::class,
            ]);
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
