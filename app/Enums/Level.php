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

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.level';
    }

    public static function colors(): array
    {
        return [
            'success' => Level::LOW,
            'warning' => Level::MEDIUM,
            'danger' => Level::HIGH,
        ];
    }

    public function color(): string
    {
        return match ($this->value) {
            Level::LOW->value => 'success',
            Level::MEDIUM->value => 'warning',
            Level::HIGH->value => 'danger',
        };
    }
}
