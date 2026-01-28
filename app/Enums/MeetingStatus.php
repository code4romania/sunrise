<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use App\Concerns\Enums;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum MeetingStatus: string implements HasLabel, HasColor
{
    use Enums\HasLabel;
    use Arrayable;
    use Comparable;

    case PLANED = 'planed';
    case REALIZED = 'realized';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.meeting_status';
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PLANED => 'warning',
            self::REALIZED => 'success',
        };
    }
}
