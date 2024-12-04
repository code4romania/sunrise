<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums;
use Filament\Support\Contracts\HasLabel;

enum ProtectionMeasuringType: string implements HasLabel
{
    use Enums\HasLabel;
    use Enums\Arrayable;
    use Enums\Comparable;

    case EMERGENCY_PLACEMENT = 'emergency_placement';
    case PLACEMENT_IN_FAMILY = 'placement_in_family';
    case PLACEMENT_AT_FOSTER_CARE = 'placement_at_foster_care';
    case PLACEMENT_IN_RESIDENTIAL_CARE_SERVICE = 'placement_in_residential_care_service';
    case SPECIALIZED_SUPERVISION = 'specialized_supervision';
    case OTHER = 'other';

    public function labelKeyPrefix(): ?string
    {
        return 'enum.protection_measuring_type';
    }
}
