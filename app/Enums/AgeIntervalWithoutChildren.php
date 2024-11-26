<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums;
use Filament\Support\Contracts\HasLabel;

enum AgeIntervalWithoutChildren: string implements HasLabel
{
    use Enums\HasLabel;
    use Enums\Arrayable;
    use Enums\Comparable;

    case BETWEEN_14_AND_17_YEARS = 'between_14_and_17_years';
    case BETWEEN_18_AND_25_YEARS = 'between_18_and_25_years';
    case BETWEEN_26_AND_35_YEARS = 'between_26_and_35_years';
    case BETWEEN_36_AND_45_YEARS = 'between_36_and_45_years';
    case BETWEEN_46_AND_55_YEARS = 'between_46_and_55_years';
    case BETWEEN_56_AND_65_YEARS = 'between_56_and_65_years';
    case OVER_65_YEARS = 'over_65_years';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.age_interval';
    }
}
