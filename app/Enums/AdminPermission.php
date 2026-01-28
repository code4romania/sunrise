<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use App\Concerns\Enums;
use Filament\Support\Contracts\HasLabel;

enum AdminPermission: string implements HasLabel
{
    use Arrayable;
    use Comparable;
    use Enums\HasLabel;

    case CAN_CHANGE_NOMENCLATURE = 'can_change_nomenclature';
    case CAN_CHANGE_STAFF = 'can_change_staff';
    case CAN_CHANGE_ORGANISATION_PROFILE = 'can_change_organisation_profile';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.admin_permission';
    }
}
