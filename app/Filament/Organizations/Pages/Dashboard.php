<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Pages;

use App\Filament\Organizations\Widgets\CaseStatsWidget;
use Filament\Facades\Filament;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\WidgetConfiguration;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;

class Dashboard extends BaseDashboard
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    public function render(): View
    {
        if (request()->get('close_config_progress') && auth()->user()->isNgoAdmin()) {
            auth()->user()->update(['config_process' => true]);
        }

        return parent::render();
    }

    public function getHeading(): string|Htmlable
    {
        return __('dashboard.welcome', [
            'name' => auth()->user()->last_name,
        ]);
    }

    /**
     * @return array<int, class-string<\Filament\Widgets\Widget>>
     */
    protected function getHeaderWidgets(): array
    {
        return [
            CaseStatsWidget::class,
        ];
    }

    /**
     * @return array<int, class-string<\Filament\Widgets\Widget>|\Filament\Widgets\WidgetConfiguration>
     */
    public function getWidgets(): array
    {
        $all = Filament::getWidgets();

        return array_values(array_filter(
            $all,
            fn (string|WidgetConfiguration $widget): bool => (is_string($widget) ? $widget : $widget->widget) !== CaseStatsWidget::class
        ));
    }

    public static function getNavigationLabel(): string
    {
        return __('dashboard.labels.navigation');
    }
}
