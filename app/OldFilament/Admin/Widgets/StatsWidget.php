<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Models\Beneficiary;
use App\Models\Institution;
use App\Models\Organization;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsWidget extends BaseWidget
{
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        return [
            Stat::make(
                __('admin.stats.total_institutions'),
                Institution::query()
                    ->count()
            ),

            Stat::make(
                __('admin.stats.total_organizations'),
                Organization::query()
                    ->count()
            ),
            Stat::make(
                __('admin.stats.beneficiaries_total_cases'),
                Beneficiary::query()->count()
            ),

            Stat::make(
                __('admin.stats.beneficiaries_open_cases'),
                Beneficiary::query()->WhereCaseIsActive()->count()
            ),

        ];
    }
}
