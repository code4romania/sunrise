<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums;
use Filament\Support\Contracts\HasLabel;

enum Helps: string implements HasLabel
{
    use Enums\Arrayable;
    use Enums\Comparable;
    use Enums\HasLabel;

    case TEMPORARY_SHELTER = 'temporary_shelter';
    case EMERGENCY_BAG_STORAGE = 'emergency_bag_storage';
    case FINANCIAL_SUPPORT = 'financial_support';
    case EMOTIONAL_SUPPORT = 'emotional_support';
    case ACCOMPANYING_ACTIONS = 'accompanying_actions';
    case EMERGENCY_CALL = 'emergency_call';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.helps';
    }
}
