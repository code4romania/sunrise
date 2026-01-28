<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums;
use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use Filament\Support\Contracts\HasLabel;

enum DashboardIntervalFilter: string implements HasLabel
{
    use Enums\HasLabel;
    use Comparable;
    use Arrayable;

    case TODAY = 'today';
    case TOMORROW = 'tomorrow';
    case ONE_WEEK = 'one_week';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.dashboard_interval_filter';
    }
}
