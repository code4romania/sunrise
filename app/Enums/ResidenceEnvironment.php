<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns;
use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use Filament\Support\Contracts\HasLabel;

enum ResidenceEnvironment: string implements HasLabel
{
    use Arrayable;
    use Comparable;
    use Concerns\Enums\HasLabel;

    case URBAN = 'urban';
    case RURAL = 'rural';
    case UNKNOWN = 'unknown';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.residence_environment';
    }
}
