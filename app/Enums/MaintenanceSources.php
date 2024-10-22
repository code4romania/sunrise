<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums;
use Filament\Support\Contracts\HasLabel;

enum MaintenanceSources: string implements HasLabel
{
    use Enums\Arrayable;
    use Enums\Comparable;
    use Enums\HasLabel;
    case RELATIONSHIP_INCOME = 'relationship_income';
    case ALIMONY = 'alimony';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.maintenance_sources';
    }
}
