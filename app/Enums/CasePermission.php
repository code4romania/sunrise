<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use App\Concerns\Enums\HasLabel;

enum CasePermission: string
{
    use Arrayable;
    use Comparable;
    use HasLabel;

    case HAS_ACCESS_TO_ALL_CASES = 'has_access_to_all_cases';
    case CAN_SEARCH_CASES_IN_ALL_CENTERS = 'can_search_cases_in_all_centers';
    case CAN_COPY_CASES_IN_ALL_CENTERS = 'can_copy_cases_in_all_centers';
    case HAS_ACCESS_TO_STATISTICS = 'has_access_to_statistics';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.case_permissions';
    }
}
