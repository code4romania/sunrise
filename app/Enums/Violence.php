<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns;
use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use Filament\Support\Contracts\HasLabel;

enum Violence: string implements HasLabel
{
    use Arrayable;
    use Comparable;
    use Concerns\Enums\HasLabel;

    case VERBAL = 'verbal';
    case PSYCHOLOGICAL = 'psychological';
    case PHYSICAL = 'physical';
    case SEXUAL = 'sexual';
    case ECONOMIC = 'economic';
    case SOCIAL = 'social';
    case SPIRITUAL = 'spiritual';
    case CYBER = 'cyber';
    case DEPRIVATION = 'deprivation';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.violence';
    }
}
