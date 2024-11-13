<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums;
use Filament\Support\Contracts\HasLabel;

enum IncomeSource: string implements HasLabel
{
    use Enums\HasLabel;
    use Enums\Arrayable;
    use Enums\Comparable;

    case SALARY = 'salary';
    case PENSION = 'pension';
    case CHILD_ALLOWANCE = 'child_allowance';
    case ALIMONY = 'alimony';
    case OTHER = 'other';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.income_source';
    }
}
