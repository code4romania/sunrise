<?php

declare(strict_types=1);

namespace App\Filament\Widgets\Organizations;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CaseStatsWidget extends BaseWidget
{
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        return [
            Stat::make(__('beneficiary.stats.open'), 25),

            Stat::make(__('beneficiary.stats.monitoring'), 21),

            Stat::make(__('beneficiary.stats.closed'), 312),
        ];
    }
}
