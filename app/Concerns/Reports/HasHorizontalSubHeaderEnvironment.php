<?php

declare(strict_types=1);

namespace App\Concerns\Reports;

use App\Enums\ResidenceEnvironment;

trait HasHorizontalSubHeaderEnvironment
{
    public function getHorizontalSubHeader(): ?array
    {
        $header = ResidenceEnvironment::options();

        if (! $this->showMissingValues) {
            return $header;
        }

        $header[null] = __('report.headers.missing_values');

        return $header;
    }

    public function getHorizontalSubHeaderKey(): ?string
    {
        return 'environment';
    }
}
