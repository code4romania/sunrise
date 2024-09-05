<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns;

enum CaseStatus: string
{
    use Concerns\Enums\Arrayable;
    use Concerns\Enums\Comparable;
    use Concerns\Enums\HasLabel;

    case ACTIVE = 'active';
    case REACTIVATED = 'reactivated';
    case MONITORED = 'monitored';
    case CLOSED = 'closed';
    case ARCHIVED = 'archived';

    protected function labelKeyPrefix(): ?string
    {
        return 'beneficiary.status';
    }
}
