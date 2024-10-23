<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums;
use Filament\Support\Contracts\HasLabel;

enum PossessionMode: string implements HasLabel
{
    use Enums\HasLabel;
    use Enums\Arrayable;
    use Enums\Comparable;

    case EXCLUSIVE_PROPERTY = 'exclusive_property';
    //case DEVALMASIE = 'devalmasie';
    case CO_OWNERSHIP = 'co_ownership';
    case RENTAL_STATE_HOUSING = 'rental_state_housing';
    case PRIVATE_HOUSING_RENTAL = 'private_housing_rental';
    case COMMODE = 'commode';
    case DONATION = 'donation';
    //case USUFRUCT = 'usufruct';
    case OTHER = 'other';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.possession_mode';
    }
}
