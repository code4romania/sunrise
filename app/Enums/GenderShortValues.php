<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use App\Concerns;
use Filament\Support\Contracts\HasLabel;

enum GenderShortValues: string implements HasLabel
{
    use Arrayable;
    use Comparable;
    use Concerns\Enums\HasLabel;

    case F = 'f';
    case M = 'm';
    case N = 'n';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.gender_short_values';
    }
}
