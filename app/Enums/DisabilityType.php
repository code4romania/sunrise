<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums;
use Filament\Support\Contracts\HasLabel;

enum DisabilityType: string implements HasLabel
{
    use Enums\HasLabel;
    use Enums\Arrayable;
    use Enums\Comparable;

    case PHYSICAL = 'physical';
    case VISUAL = 'visual';
    case AURAL = 'aural';
    case DEAF = 'deaf';
    case SOMATIC = 'somatic';
    case MENTAL = 'mental';
    case PSYCHIC = 'psychic';
    case HIV_AIDS = 'hiv_aids';
    case ASSOCIATE = 'associate';
    case RARE_DISEASES = 'rare_diseases';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.disability_type';
    }
}
