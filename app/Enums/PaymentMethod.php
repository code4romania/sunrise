<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums;
use Filament\Support\Contracts\HasLabel;

enum PaymentMethod: string implements HasLabel
{
    use Enums\HasLabel;
    use Enums\Arrayable;
    use Enums\Comparable;

    case REPRESENTATIVE = 'representative';
    case POSTAL_OFFICE = 'postal_office';
    case BANK_ACCOUNT = 'bank_account';
    case OTHER = 'other';

    public function labelKeyPrefix(): ?string
    {
        return 'enum.payment_method';
    }
}
