<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns;
use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use Filament\Support\Contracts\HasLabel;

enum Ternary: int implements HasLabel
{
    use Arrayable;
    use Comparable;
    use Concerns\Enums\HasLabel;

    case YES = 1;
    case NO = 0;
    case UNKNOWN = -1;

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.ternary';
    }

    public static function isYes(mixed $subject): bool
    {
        return self::isValue($subject, self::YES);
    }

    public static function isNo(mixed $subject): bool
    {
        return self::isValue($subject, self::NO);
    }

    public static function isUnknown(mixed $subject): bool
    {
        return self::isValue($subject, self::UNKNOWN);
    }
}
