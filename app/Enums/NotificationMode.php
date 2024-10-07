<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns;
use Filament\Support\Contracts\HasLabel;

enum NotificationMode: string implements HasLabel
{
    use Concerns\Enums\Arrayable;
    use Concerns\Enums\Comparable;
    use Concerns\Enums\HasLabel;

    case PHONE = 'phone';
    case PERSONAL = 'personal';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.notification_mode';
    }
}
