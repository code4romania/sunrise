<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use App\Concerns;
use Filament\Support\Contracts\HasLabel;

enum OrganizationType: string implements HasLabel
{
    use Arrayable;
    use Comparable;
    use Concerns\Enums\HasLabel;

    case NGO = 'ngo';
    case PUBLIC = 'public';
    case OTHER_TYPE = 'other_type';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.organization_type';
    }
}
