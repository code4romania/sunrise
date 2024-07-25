<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use App\Concerns\Enums\HasColor;
use App\Concerns\Enums\HasLabel;

enum Level: string
{
    use Arrayable;
    use Comparable;
    use HasLabel;
    use HasColor;

    case HIGH = 'high';
    case MEDIUM = 'medium';
    case LOW = 'low';
    case NONE = 'none';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.level';
    }

    public static function colors(): array
    {
        return [
            'success' => Level::NONE,
            'warning' => [Level::MEDIUM, Level::LOW],
            'danger' => Level::HIGH,
        ];
    }

    public function color(): string
    {
        return match ($this->value) {
            Level::NONE->value => 'success',
            Level::LOW->value => 'warning',
            Level::MEDIUM->value => 'warning',
            Level::HIGH->value => 'danger',
        };
    }

    public function icon(): string
    {
        return match ($this->value) {
            Level::NONE->value => 'heroicon-s-check-circle',
            Level::LOW->value => 'heroicon-s-exclamation-triangle',
            Level::MEDIUM->value => 'heroicon-s-exclamation-triangle',
            Level::HIGH->value => 'heroicon-s-exclamation-triangle',
        };
    }
}
