<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums;
use Filament\Support\Contracts\HasLabel;

enum Frequency: string implements HasLabel
{
    use Enums\Arrayable;
    use Enums\Comparable;
    use Enums\HasLabel;

    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
    case LASS_THAN_MONTHLY = 'lass_than_monthly';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.frequency';
    }
}
