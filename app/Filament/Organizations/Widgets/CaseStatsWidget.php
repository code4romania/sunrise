<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Widgets;

use App\Models\Beneficiary;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CaseStatsWidget extends BaseWidget
{
    protected static bool $isLazy = false;

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        return [
            Stat::make(
                __('beneficiary.stats.open'),
                Beneficiary::query()
                    ->whereCaseIsActive()
                    ->whereUserHasAccess()
                    ->count()
            ),

            Stat::make(
                __('beneficiary.stats.monitoring'),
                Beneficiary::query()
                    ->whereCaseIsMonitored()
                    ->whereUserHasAccess()
                    ->count()
            ),

            Stat::make(
                __('beneficiary.stats.closed'),
                Beneficiary::query()
                    ->whereCaseIsClosed()
                    ->whereUserHasAccess()
                    ->count()
            ),
        ];
    }
}
