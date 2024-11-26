<?php

declare(strict_types=1);

namespace App\Concerns\Reports;

use App\Enums\ResidenceEnvironment;

trait HasVerticalSubHeaderEnvironment
{
    public function getVerticalSubHeader(): ?array
    {
        $header = ResidenceEnvironment::options();

        if (! $this->showMissingValues) {
            return $header;
        }

        $header[null] = __('report.headers.missing_values');

        return $header;
    }

    public function getVerticalSubHeaderKey(): ?string
    {
        return 'environment';
    }
}
