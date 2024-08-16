<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns;
use Filament\Support\Contracts\HasLabel;

enum CaseStatus: string implements HasLabel
{
    use Concerns\Enums\Arrayable;
    use Concerns\Enums\Comparable;
    use Concerns\Enums\HasLabel;

    case ACTIVE = 'active';
    case REACTIVATED = 'reactivated';
    case MONITORED = 'monitored';
    case CLOSED = 'closed';

    protected function labelKeyPrefix(): ?string
    {
        return 'beneficiary.status';
    }
}
