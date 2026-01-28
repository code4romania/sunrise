<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums;
use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use Filament\Support\Contracts\HasLabel;

enum Diseases: string implements HasLabel
{
    use Enums\HasLabel;
    use Arrayable;
    use Comparable;

    case DENIES_DISEASES = 'denies_diseases';
    case CHRONIC_DISEASES = 'chronic_diseases';
    case DEGENERATIVE_DISEASES = 'degenerative_diseases';
    case MENTAL_ILLNESSES = 'mental_illnesses';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.diseases';
    }
}
