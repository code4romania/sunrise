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
        $query = Beneficiary::query()->whereUserHasAccess();

        return [
            Stat::make(
                __('beneficiary.stats.open'),
                (clone $query)->whereCaseIsActive()->count()
            ),
            Stat::make(
                __('beneficiary.stats.monitoring'),
                (clone $query)->whereCaseIsMonitored()->count()
            ),
            Stat::make(
                __('beneficiary.stats.total'),
                $query->count()
            ),
        ];
    }
}
