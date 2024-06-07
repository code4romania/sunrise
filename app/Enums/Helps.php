<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use App\Concerns\Enums\HasLabel;

enum Helps: string
{
    use Arrayable;
    use Comparable;
    use HasLabel;

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
