<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Concerns\Reports\HasVerticalHeaderStudies;
use App\Interfaces\ReportGenerator;

class CasesByStudies extends BaseGenerator implements ReportGenerator
{
    use HasVerticalHeaderStudies;

    public function getHorizontalHeader(): array
    {
        return [
            __('report.headers.studies'),
            __('report.headers.case_distribution'),
        ];
    }

    public function getSelectedFields(): array|string
    {
        return 'studies';
    }

    public function addRelatedTables(): void
    {
        $this->query->join('beneficiary_details', 'beneficiary_details.beneficiary_id', '=', 'beneficiaries.id');
    }
}
