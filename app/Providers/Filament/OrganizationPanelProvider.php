<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Organizations\Pages\Auth\Login;
use App\Filament\Organizations\Pages\Dashboard;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use App\Filament\Organizations\Pages;
use App\Filament\Pages\Auth\RequestPasswordReset;
use App\Http\Middleware\EnsureUserIsActive;
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
        // UserPersonalInfo is now a standalone Livewire component
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('organization')
            ->sidebarCollapsibleOnDesktop()
            ->collapsibleNavigationGroups(false)
            ->login(Login::class)
            ->passwordReset(RequestPasswordReset::class)
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
                for: 'App\\Filament\\Organizations\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/Organizations/Pages'),
                for: 'App\\Filament\\Organizations\\Pages'
            )
            ->pages([
                Dashboard::class,
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
                Action::configureUsing(function (Action $action) {
                    $action->modalFooterActionsAlignment(Alignment::Right);
                });
            })
            ->unsavedChangesAlerts()
            // ->databaseNotifications()
            ->plugins([
                // Breezy removed - using native Filament profile
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
                EnsureUserIsActive::class,
            ])
            ->tenant(Organization::class, 'slug')
            ->tenantRoutePrefix('org')
            ->tenantMiddleware([
                UpdateDefaultTenant::class,
            ]);
    }

    protected function setDefaultDateTimeDisplayFormats(): void
    {
        Table::configureUsing(fn(Table $table) => $table->defaultDateDisplayFormat(static::$defaultDateDisplayFormat));
        Table::configureUsing(fn(Table $table) => $table->defaultDateTimeDisplayFormat(static::$defaultDateTimeDisplayFormat));
        Table::configureUsing(fn(Table $table) => $table->defaultTimeDisplayFormat(static::$defaultTimeDisplayFormat));

        Schema::configureUsing(fn(Schema $schema) => $schema->defaultDateDisplayFormat(static::$defaultDateDisplayFormat));
        Schema::configureUsing(fn(Schema $schema) => $schema->defaultDateTimeDisplayFormat(static::$defaultDateTimeDisplayFormat));
        Schema::configureUsing(fn(Schema $schema) => $schema->defaultTimeDisplayFormat(static::$defaultTimeDisplayFormat));

        DateTimePicker::configureUsing(fn(DateTimePicker $dateTimePicker) => $dateTimePicker->defaultDateDisplayFormat(static::$defaultDateDisplayFormat));
        DateTimePicker::configureUsing(fn(DateTimePicker $dateTimePicker) => $dateTimePicker->defaultDateTimeDisplayFormat(static::$defaultDateTimeDisplayFormat));
        DateTimePicker::configureUsing(fn(DateTimePicker $dateTimePicker) => $dateTimePicker->defaultDateTimeWithSecondsDisplayFormat(static::$defaultDateTimeWithSecondsDisplayFormat));
        DateTimePicker::configureUsing(fn(DateTimePicker $dateTimePicker) => $dateTimePicker->defaultTimeDisplayFormat(static::$defaultTimeDisplayFormat));
        DateTimePicker::configureUsing(fn(DateTimePicker $dateTimePicker) => $dateTimePicker->defaultTimeWithSecondsDisplayFormat(static::$defaultTimeWithSecondsDisplayFormat));
    }
}
