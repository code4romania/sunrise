<?php

declare(strict_types=1);

namespace App\Concerns\Reports;

use App\Enums\HomeOwnership;

trait HasVerticalHeaderHomeOwnership
{
    public function getVerticalHeader(): array
    {
        $header = HomeOwnership::options();

        if (! $this->showMissingValues) {
            return $header;
        }

        $header[null] = __('report.headers.missing_values');

        return $header;
    }

    public function getVerticalHeaderKey(): string
    {
        return 'homeownership';
    }
}
