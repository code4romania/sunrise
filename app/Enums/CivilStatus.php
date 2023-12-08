<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns;

enum CivilStatus: string
{
    use Concerns\Enums\Arrayable;
    use Concerns\Enums\Comparable;
    use Concerns\Enums\HasLabel;

    case SINGLE = 'single';
    case MARRIED = 'married';
    case DIVORCED = 'divorced';
    case WIDOWED = 'widowed';
    case COHABITATION = 'cohabitation';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.civil_status';
    }
}
