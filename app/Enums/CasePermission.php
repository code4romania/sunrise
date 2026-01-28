<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use App\Concerns\Enums;
use Filament\Support\Contracts\HasLabel;

enum CasePermission: string implements HasLabel
{
    use Arrayable;
    use Comparable;
    use Enums\HasLabel;

    case HAS_ACCESS_TO_ALL_CASES = 'has_access_to_all_cases';
    case CAN_SEARCH_AND_COPY_CASES_IN_ALL_CENTERS = 'can_search_and_copy_cases_in_all_centers';
    case HAS_ACCESS_TO_STATISTICS = 'has_access_to_statistics';

    case CAN_BE_CASE_MANAGER = 'can_be_case_manager';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.case_permissions';
    }

    public static function getOptionsWithoutCaseManager(): array
    {
        $options = self::options();
        unset($options[self::CAN_BE_CASE_MANAGER->value]);

        return $options;
    }
}
