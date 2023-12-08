<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns;

enum ResidenceEnvironment: string
{
    use Concerns\Enums\Arrayable;
    use Concerns\Enums\Comparable;
    use Concerns\Enums\HasLabel;

    case URBAN = 'urban';
    case RURAL = 'rural';
    case UNKNOWN = 'unknown';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.residence_environment';
    }
}
