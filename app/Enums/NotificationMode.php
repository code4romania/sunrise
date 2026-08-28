<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns;
use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use Filament\Support\Contracts\HasLabel;

enum NotificationMode: string implements HasLabel
{
    use Arrayable;
    use Comparable;
    use Concerns\Enums\HasLabel;

    case PHONE = 'phone';
    case PERSONAL = 'personal';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.notification_mode';
    }
}
