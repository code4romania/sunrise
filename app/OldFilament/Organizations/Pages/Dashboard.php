<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;

class Dashboard extends BaseDashboard
{
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

    public static function getNavigationLabel(): string
    {
        return __('dashboard.labels.navigation');
    }
}
