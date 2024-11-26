<?php

declare(strict_types=1);

namespace App\Concerns\Reports;

use App\Enums\AgeInterval;

trait HasHorizontalSubHeaderAgeInterval
{
    public function getHorizontalSubHeader(): ?array
    {
        $header = AgeInterval::options();

        if (! $this->showMissingValues) {
            return $header;
        }

        $header['unknown'] = __('report.headers.missing_values');

        return $header;
    }

    public function getHorizontalSubHeaderKey(): ?string
    {
        return 'age_group';
    }
}
