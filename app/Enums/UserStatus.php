<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use App\Concerns\Enums\HasLabel;

enum UserStatus: string
{
    use Arrayable;
    use Comparable;
    use HasLabel;

    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case PENDING = 'pending';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.user_status';
    }
}
