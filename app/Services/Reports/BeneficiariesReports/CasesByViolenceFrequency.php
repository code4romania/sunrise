<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Concerns\Reports\InteractWithViolence;
use App\Enums\Frequency;
use App\Interfaces\ReportGenerator;

class CasesByViolenceFrequency extends BaseGenerator implements ReportGenerator
{
    use InteractWithViolence;

    public function getHorizontalHeader(): array
    {
        return  [
            __('report.headers.frequency_violence'),
            __('report.headers.case_distribution'),
        ];
    }

    public function getVerticalHeader(): array
    {
        $header = Frequency::options();

        if (! $this->showMissingValues) {
            return $header;
        }

        $header[null] = __('report.headers.missing_values');

        return $header;
    }

    public function getVerticalHeaderKey(): string
    {
        return 'frequency_violence';
    }

    public function getSelectedFields(): array|string
    {
        return 'frequency_violence';
    }
}
