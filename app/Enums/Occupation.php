<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns;
use Filament\Support\Contracts\HasLabel;

enum Occupation: string implements HasLabel
{
    use Concerns\Enums\Arrayable;
    use Concerns\Enums\Comparable;
    use Concerns\Enums\HasLabel;

    case NONE = 'none';
    case EMPLOYEE = 'employee';
    case SELF_EMPLOYED = 'self_employed';
    case ASSOCIATION_WORKER = 'association_worker';
    case BUSINESS_OWNER = 'business_owner';
    case FARMER = 'farmer';
    case STUDENT = 'student';
    case UNEMPLOYED = 'unemployed';
    case DOMESTIC = 'domestic';
    case RETIRED = 'retired';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.occupation';
    }
}
