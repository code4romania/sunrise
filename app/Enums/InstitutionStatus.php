<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use App\Concerns\Enums\HasLabel as HasLabelTrait;
use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum InstitutionStatus: string implements HasLabel, HasColor
{
    use Arrayable;
    use Comparable;
    use HasLabelTrait;

    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case PENDING = 'pending';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.institution_status';
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::ACTIVE => Color::Green,
            self::INACTIVE => Color::Red,
            self::PENDING => Color::Yellow,
        };
    }
}
