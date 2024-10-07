<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums;
use Filament\Support\Contracts\HasLabel;

enum AdmittanceReason: string implements HasLabel
{
    use Enums\HasLabel;
    use Enums\Arrayable;
    use Enums\Comparable;

    case SECURITY = 'security';
    case EVICTION_FROM_HOME = 'eviction_from_home';
    case DIVORCE = 'divorce';
    case CRISIS_SITUATION = 'crisis_situation';
    case OTHER = 'other';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.admittance_reason';
    }
}
