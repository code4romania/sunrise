<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns;
use Filament\Support\Contracts\HasLabel;

enum GenderShortValues: string implements HasLabel
{
    use Concerns\Enums\Arrayable;
    use Concerns\Enums\Comparable;
    use Concerns\Enums\HasLabel;

    case F = 'f';
    case M = 'm';
    case N = 'n';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.gender_short_values';
    }
}
