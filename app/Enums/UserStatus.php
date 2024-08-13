<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use App\Concerns\Enums\HasLabel as HasLabelTrait;
use Filament\Support\Contracts\HasLabel;

enum UserStatus: string implements HasLabel
{
    use Arrayable;
    use Comparable;
    use HasLabelTrait;

    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case PENDING = 'pending';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.user_status';
    }
}
