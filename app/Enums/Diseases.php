<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums;
use Filament\Support\Contracts\HasLabel;

enum Diseases: string implements HasLabel
{
    use Enums\HasLabel;
    use Enums\Arrayable;
    use Enums\Comparable;

    case DENIES_DISEASES = 'denies_diseases';
    case CHRONIC_DISEASES = 'chronic_diseases';
    case DEGENERATIVE_DISEASES = 'degenerative_diseases';
    case MENTAL_ILLNESSES = 'mental_illnesses';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.diseases';
    }
}
