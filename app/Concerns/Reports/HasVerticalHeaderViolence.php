<?php

declare(strict_types=1);

namespace App\Concerns\Reports;

use App\Enums\Violence;

trait HasVerticalHeaderViolence
{
    public function getVerticalHeader(): array
    {
        $header = Violence::options();

        if (! $this->showMissingValues) {
            return $header;
        }

        $header[null] = __('report.headers.missing_values');

        return $header;
    }

    public function getVerticalHeaderKey(): string
    {
        return 'violence_primary_type';
    }
}
