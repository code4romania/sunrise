<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Widgets;

use App\Models\Beneficiary;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CaseStatsWidget extends BaseWidget
{
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        return [
            Stat::make(
                __('beneficiary.stats.open'),
                Beneficiary::query()
                    ->count()
            ),

            Stat::make(
                __('beneficiary.stats.monitoring'),
                Beneficiary::query()
                    // ->where('status', 'monitoring')
                    ->count()
            ),

            Stat::make(
                __('beneficiary.stats.closed'),
                Beneficiary::query()
                    // ->where('status', 'closed')
                    ->count()
            ),
        ];
    }
}
