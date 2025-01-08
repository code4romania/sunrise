<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends BaseDashboard
{
    public function getHeading(): string | Htmlable
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
