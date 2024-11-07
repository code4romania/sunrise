<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums;
use Filament\Support\Contracts\HasLabel;

enum CasePermission: string implements HasLabel
{
    use Enums\Arrayable;
    use Enums\Comparable;
    use Enums\HasLabel;

    case HAS_ACCESS_TO_ALL_CASES = 'has_access_to_all_cases';
    case CAN_SEARCH_AND_COPY_CASES_IN_ALL_CENTERS = 'can_search_and_copy_cases_in_all_centers';
    case HAS_ACCESS_TO_STATISTICS = 'has_access_to_statistics';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.case_permissions';
    }
}
