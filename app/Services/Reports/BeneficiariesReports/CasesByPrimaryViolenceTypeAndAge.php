<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Concerns\Reports\HasVerticalHeaderViolence;
use App\Enums\BeneficiarySegmentationByAge;
use App\Interfaces\ReportGenerator;

class CasesByPrimaryViolenceTypeAndAge extends BaseGenerator implements ReportGenerator
{
    use HasVerticalHeaderViolence;

    public function getHorizontalHeader(): array
    {
        return [
            __('report.headers.primary_violence'),
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

    public function getVerticalHeaderKey(): string
    {
        return 'violence_primary_type';
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
        ];
    }

    public function addRelatedTables(): void
    {
        $this->query->join('violences', 'violences.beneficiary_id', '=', 'beneficiaries.id');
    }
}
