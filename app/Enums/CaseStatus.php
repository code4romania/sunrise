<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns;
use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CaseStatus: string implements HasLabel, HasColor
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

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::MONITORED => 'warning',
            self::CLOSED => Color::Gray,
            self::ARCHIVED => 'primary',
            default => 'danger',
        };
    }
}
