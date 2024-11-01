<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Concerns\Reports\HasVerticalHeaderStudies;
use App\Enums\BeneficiarySegmentationByAge;
use App\Interfaces\ReportGenerator;

class CasesByStudiesAndAge extends BaseGenerator implements ReportGenerator
{
    use HasVerticalHeaderStudies;

    public function getHorizontalHeader(): array
    {
        return [
            __('report.headers.studies'),
            __('report.headers.cases_by_age_segmentation'),
            __('report.headers.subtotal'),
        ];
    }

    public function getHorizontalSubHeader(): ?array
    {
        return BeneficiarySegmentationByAge::options();
    }

    public function getHorizontalSubHeaderKey(): ?string
    {
        return 'age_group';
    }

    public function getSelectedFields(): array|string
    {
        return [
            'studies',
            "CASE
                WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) < 17 THEN 'minor'
                WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) > 18 THEN 'major'
                ELSE 'unknown'
            END as age_group",
        ];
    }

    public function addRelatedTables(): void
    {
        $this->query->join('beneficiary_details', 'beneficiary_details.beneficiary_id', '=', 'beneficiaries.id');
    }
}
