<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Concerns\Reports\HasVerticalHeaderViolence;
use App\Concerns\Reports\InteractWithViolence;
use App\Enums\BeneficiarySegmentationByAge;
use App\Enums\Frequency;
use App\Interfaces\ReportGenerator;

class CasesByPrimaryViolenceFrequencyAndAge extends BaseGenerator implements ReportGenerator
{
    use HasVerticalHeaderViolence;
    use InteractWithViolence;

    public function getHorizontalHeader(): array
    {
        return [
            __('report.headers.primary_violence_and_age'),
            __('report.headers.cases_by_frequency_violence'),
            __('report.headers.subtotal'),
        ];
    }

    public function getHorizontalSubHeader(): ?array
    {
        $header = Frequency::options();

        if (! $this->showMissingValues) {
            return $header;
        }

        $header[null] = __('report.headers.missing_values');

        return $header;
    }

    public function getHorizontalSubHeaderKey(): ?string
    {
        return 'frequency_violence';
    }

    public function getVerticalSubHeader(): ?array
    {
        $header = BeneficiarySegmentationByAge::options();

        if (! $this->showMissingValues) {
            return $header;
        }

        $header['unknown'] = __('report.headers.missing_values');

        return $header;
    }

    public function getVerticalSubHeaderKey(): ?string
    {
        return 'age_group';
    }

    public function getSelectedFields(): array|string
    {
        return [
            "CASE
                WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) < 17 THEN 'minor'
                WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) > 18 THEN 'major'
                ELSE 'unknown'
            END as age_group",
            'violence_primary_type',
            'frequency_violence',
        ];
    }
}
