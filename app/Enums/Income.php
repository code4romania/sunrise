<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use App\Concerns;
use Filament\Support\Contracts\HasLabel;

enum Income: string implements HasLabel
{
    use Arrayable;
    use Comparable;
    use Concerns\Enums\HasLabel;

    case NONE = 'none';
    case BELOW_MINIMUM = 'below_minimum';
    case BETWEEN_MINIMUM_AVERAGE = 'between_minimum_average';
    case ABOVE_AVERAGE = 'above_average';
    case SOCIAL_BENEFITS = 'social_benefits';
    case OTHER = 'other';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.income';
    }
}
