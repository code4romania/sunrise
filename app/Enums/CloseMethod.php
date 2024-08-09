<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums;
use Filament\Support\Contracts\HasLabel;

enum CloseMethod: string implements HasLabel
{
    use Enums\HasLabel;
    use Enums\Arrayable;
    use Enums\Comparable;

    case ACCORDING_TO_INTERVENTIONAL_PROGRAM = 'according_to_interventional_program';
    case TRANSFER_TO = 'transfer_to';
    case CONTRACT_EXPIRED = 'contract_expired';
    case DEREGISTRATION = 'deregistration';
    case TURN_TO_RELATIONSHIP_WITH_AGGRESSOR = 'turn_to_relationship_with_aggressor';
    case BENEFICIARY_REQUEST = 'beneficiary_request';
    case OTHER = 'other';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.close_method';
    }
}
