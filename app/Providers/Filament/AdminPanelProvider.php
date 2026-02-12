<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\NomenclatorPage;
use App\Http\Middleware\EnsureUserIsActive;
use App\Livewire\Welcome;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Schemas\Schema;
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

class AdminPanelProvider extends PanelProvider
{
    private const AdminNamespace = 'App\\Filament\\Admin';

    public static string $defaultDateDisplayFormat = 'Y-m-d';

    public static string $defaultDateTimeDisplayFormat = 'Y-m-d H:i';

    public static string $defaultDateTimeWithSecondsDisplayFormat = 'Y-m-d H:i:s';

    public static string $defaultTimeDisplayFormat = 'H:i';

    public static string $defaultTimeWithSecondsDisplayFormat = 'H:i:s';

    public function register(): void
    {
        parent::register();

        $this->setDefaultDateTimeDisplayFormats();
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->login()
            ->default()
            ->sidebarCollapsibleOnDesktop()
            ->collapsibleNavigationGroups(false)
            ->colors([
                'primary' => Color::Violet,
            ])
            ->font('DM Sans')
            ->maxContentWidth('full')
            ->viteTheme('resources/css/filament/common/theme.css')
            ->brandLogo(fn () => view('filament.brand'))
            ->brandLogoHeight('3rem')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->darkMode(false)
            ->discoverResources(
                in: app_path('Filament/Admin/Resources'),
                for: self::AdminNamespace.'\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/Admin/Pages'),
                for: self::AdminNamespace.'\\Pages'
            )
            ->routes(function () {
                Route::get('/welcome/{user:ulid}', Welcome::class)->name('auth.welcome');
            })
            ->discoverWidgets(
                in: app_path('Filament/Admin/Widgets'),
                for: self::AdminNamespace.'\\Widgets'
            )
            ->bootUsing(function () {
                Page::stickyFormActions();
                Page::alignFormActionsEnd();
                Action::configureUsing(function (Action $action) {
                    $action->modalFooterActionsAlignment(Alignment::Right);
                });
            })
            ->navigationItems([
                NavigationItem::make(__('nomenclature.titles.list'))
                    ->icon('heroicon-o-rectangle-stack')
                    ->sort(2)
                    ->isActiveWhen(
                        fn () => request()->routeIs('filament.admin.pages.nomenclator-page')
                    )
                    ->url(fn () => NomenclatorPage::getUrl()),
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

    protected function setDefaultDateTimeDisplayFormats(): void
    {
        Table::configureUsing(fn (Table $table) => $table->defaultDateDisplayFormat(static::$defaultDateDisplayFormat));
        Table::configureUsing(fn (Table $table) => $table->defaultDateTimeDisplayFormat(static::$defaultDateTimeDisplayFormat));
        Table::configureUsing(fn (Table $table) => $table->defaultTimeDisplayFormat(static::$defaultTimeDisplayFormat));

        Schema::configureUsing(fn (Schema $schema) => $schema->defaultDateDisplayFormat(static::$defaultDateDisplayFormat));
        Schema::configureUsing(fn (Schema $schema) => $schema->defaultDateTimeDisplayFormat(static::$defaultDateTimeDisplayFormat));
        Schema::configureUsing(fn (Schema $schema) => $schema->defaultTimeDisplayFormat(static::$defaultTimeDisplayFormat));

        DateTimePicker::configureUsing(fn (DateTimePicker $dateTimePicker) => $dateTimePicker->defaultDateDisplayFormat(static::$defaultDateDisplayFormat));
        DateTimePicker::configureUsing(fn (DateTimePicker $dateTimePicker) => $dateTimePicker->defaultDateTimeDisplayFormat(static::$defaultDateTimeDisplayFormat));
        DateTimePicker::configureUsing(fn (DateTimePicker $dateTimePicker) => $dateTimePicker->defaultDateTimeWithSecondsDisplayFormat(static::$defaultDateTimeWithSecondsDisplayFormat));
        DateTimePicker::configureUsing(fn (DateTimePicker $dateTimePicker) => $dateTimePicker->defaultTimeDisplayFormat(static::$defaultTimeDisplayFormat));
        DateTimePicker::configureUsing(fn (DateTimePicker $dateTimePicker) => $dateTimePicker->defaultTimeWithSecondsDisplayFormat(static::$defaultTimeWithSecondsDisplayFormat));
    }
}
