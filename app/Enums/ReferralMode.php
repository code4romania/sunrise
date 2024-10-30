<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns;
use Filament\Support\Contracts\HasLabel;

enum ReferralMode: string implements HasLabel
{
    use Concerns\Enums\Arrayable;
    use Concerns\Enums\Comparable;
    use Concerns\Enums\HasLabel;

    case VERBAL = 'verbal';
    case WRITTEN = 'written';
    case PHONE = 'phone';
    case BROUGHT = 'brought';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.referral_mode';
    }
}
