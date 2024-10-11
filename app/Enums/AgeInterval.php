<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums;
use Filament\Support\Contracts\HasLabel;

enum AgeInterval: string implements HasLabel
{
    use Enums\HasLabel;
    use Enums\Arrayable;
    use Enums\Comparable;

    case UNDER_1_YEAR = 'under_1_year';
    case BETWEEN_1_AND_2_YEARS = 'between_1_and_2_years';
    case BETWEEN_3_AND_6_YEARS = 'between_3_and_6_years';
    case BETWEEN_7_AND_9_YEARS = 'between_7_and_9_years';
    case BETWEEN_10_AND_13_YEARS = 'between_10_and_13_years';
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
