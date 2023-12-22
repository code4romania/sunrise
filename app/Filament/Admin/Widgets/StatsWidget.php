<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Models\Organization;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsWidget extends BaseWidget
{
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        return [
            Stat::make(
                __('organization.stats.total'),
                Organization::query()
                    ->count()
            ),

            Stat::make(
                __('user.stats.total'),
                User::query()
                    ->count()
            ),
        ];
    }
}
