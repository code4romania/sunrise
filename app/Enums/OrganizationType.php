<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns;

enum OrganizationType: string
{
    use Concerns\Enums\Arrayable;
    use Concerns\Enums\Comparable;
    use Concerns\Enums\HasLabel;

    case NGO = 'ngo';
    case PUBLIC = 'public';
    case OTHER_TYPE = 'other_type';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.organization_type';
    }
}
