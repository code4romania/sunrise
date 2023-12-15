<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns;

enum Drug: string
{
    use Concerns\Enums\Arrayable;
    use Concerns\Enums\Comparable;
    use Concerns\Enums\HasLabel;

    case ALCOHOL_OCCASIONAL = 'alcohol_occasional';
    case ALCOHOL_FREQUENT = 'alcohol_frequent';
    case TOBACCO = 'tobacco';
    case TRANQUILIZERS = 'tranquilizers';
    case DRUGS = 'drugs';
    case OTHER = 'other';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.drug';
    }
}
