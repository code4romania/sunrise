<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use App\Concerns\Enums\HasLabel;
use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;

enum Level: string implements HasColor, HasIcon
{
    use Arrayable;
    use Comparable;
    use HasLabel;

    case HIGH = 'high';
    case MEDIUM = 'medium';
    case LOW = 'low';
    case NONE = 'none';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.level';
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::HIGH => Color::Red,
            self::MEDIUM => Color::Orange,
            self::LOW => Color::Amber,
            self::NONE => Color::Green,
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::HIGH => 'heroicon-s-exclamation-triangle',
            self::MEDIUM => 'heroicon-s-exclamation-triangle',
            self::LOW => 'heroicon-s-exclamation-triangle',
            self::NONE => 'heroicon-s-check-circle',
        };
    }
}
