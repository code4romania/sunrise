<?php

declare(strict_types=1);

namespace App\Concerns\Reports;

use App\Enums\Studies;

trait HasVerticalHeaderStudies
{
    public function getVerticalHeader(): array
    {
        $header = Studies::options();

        if (! $this->showMissingValues) {
            return $header;
        }

        $header[null] = __('report.headers.missing_values');

        return $header;
    }

    public function getVerticalHeaderKey(): string
    {
        return 'studies';
    }
}
