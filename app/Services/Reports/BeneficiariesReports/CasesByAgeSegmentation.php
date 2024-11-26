<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Enums\BeneficiarySegmentationByAge;
use App\Interfaces\ReportGenerator;

class CasesByAgeSegmentation extends BaseGenerator implements ReportGenerator
{
    public function getHorizontalHeader(): array
    {
        return [
            __('report.headers.age'),
            __('report.headers.case_distribution'),
        ];
    }

    public function getVerticalHeader(): array
    {
        $header = BeneficiarySegmentationByAge::options();

        if (! $this->showMissingValues) {
            return $header;
        }

        $header['unknown'] = __('report.headers.missing_values');

        return $header;
    }

    public function getVerticalHeaderKey(): string
    {
        return 'age_group';
    }

    public function getSelectedFields(): array|string
    {
        return "CASE
                WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) < 17 THEN 'minor'
                WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) > 18 THEN 'major'
                ELSE 'unknown'
            END as age_group";
    }
}
