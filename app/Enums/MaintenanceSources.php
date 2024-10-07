<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use App\Concerns\Enums\HasLabel;

enum MaintenanceSources: string
{
    use Arrayable;
    use Comparable;
    use HasLabel;

    case RELATIONSHIP_INCOME = 'relationship_income';
    case ALIMONY = 'alimony';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.maintenance_sources';
    }
}
