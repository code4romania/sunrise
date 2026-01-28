<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use App\Concerns\Enums;
use Filament\Support\Contracts\HasLabel;

enum DisabilityDegree: string implements HasLabel
{
    use Enums\HasLabel;
    use Arrayable;
    use Comparable;

    case EASY = 'easy';
    case ENVIRONMENT = 'environment';
    case ACCENTED = 'accented';
    case SERIOUS = 'serious';
    case NO_CLASSIFICATION = 'no_classification';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.disability_degree';
    }
}
