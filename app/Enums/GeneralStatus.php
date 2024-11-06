<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use App\Concerns\Enums\HasLabel as HasLabelTrait;
use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum GeneralStatus: int implements HasLabel, HasColor
{
    use Arrayable;
    use Comparable;
    use HasLabelTrait;

    case ACTIVE = 1;
    case INACTIVE = 0;

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.general_status';
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::INACTIVE => Color::Gray,
        };
    }
}
