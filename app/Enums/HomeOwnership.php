<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns;

enum HomeOwnership: string
{
    use Concerns\Enums\Arrayable;
    use Concerns\Enums\Comparable;
    use Concerns\Enums\HasLabel;

    case NONE = 'none';
    case PROPERTY_OF_VICTIM = 'property_of_victim';
    case PROPERTY_OF_AGGRESSOR = 'property_of_aggressor';
    case JOINT = 'joint';
    case FAMILY_OF_VICTIM = 'family_of_victim';
    case FAMILY_OF_AGGRESSOR = 'family_of_aggressor';
    case RENT = 'rent';
    case OTHER = 'other';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.homeownership';
    }
}
