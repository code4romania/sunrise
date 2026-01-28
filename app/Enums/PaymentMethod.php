<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use App\Concerns\Enums;
use Filament\Support\Contracts\HasLabel;

enum PaymentMethod: string implements HasLabel
{
    use Enums\HasLabel;
    use Arrayable;
    use Comparable;
    case POSTAL_OFFICE = 'postal_office';
    case BANK_ACCOUNT = 'bank_account';
    case OTHER = 'other';

    public function labelKeyPrefix(): ?string
    {
        return 'enum.payment_method';
    }
}
