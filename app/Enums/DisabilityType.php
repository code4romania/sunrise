<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums;
use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use Filament\Support\Contracts\HasLabel;

enum DisabilityType: string implements HasLabel
{
    use Enums\HasLabel;
    use Arrayable;
    use Comparable;

    case PHYSICAL = 'physical';
    case VISUAL = 'visual';
    case AURAL = 'aural';
    case DEAF = 'deaf';
    case SOMATIC = 'somatic';
    case MENTAL = 'mental';
    case NEUROPSYCHIC = 'neuropsychic';
    case HIV_AIDS = 'hiv_aids';
    case ASSOCIATE = 'associate';
    case RARE_DISEASES = 'rare_diseases';

    case UNKNOWN = 'unknown';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.disability_type';
    }
}
