<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns;

enum NotificationMode: string
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
