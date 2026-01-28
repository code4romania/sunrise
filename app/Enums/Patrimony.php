<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use App\Concerns\Enums;
use Filament\Support\Contracts\HasLabel;

enum Patrimony: string implements HasLabel
{
    use Enums\HasLabel;
    use Arrayable;
    use Comparable;

    case APARTMENT = 'apartment';
    case HOUSE = 'house';
    case WITHOUT = 'without';
    case UNKNOWN = 'unknown';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.patrimony';
    }
}
