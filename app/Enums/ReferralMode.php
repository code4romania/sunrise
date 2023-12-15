<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns;

enum ReferralMode: string
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
