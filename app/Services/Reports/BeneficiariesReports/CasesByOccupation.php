<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Concerns\Reports\HasVerticalHeaderOccupation;
use App\Interfaces\ReportGenerator;

class CasesByOccupation extends BaseGenerator implements ReportGenerator
{
    use HasVerticalHeaderOccupation;

    public function getHorizontalHeader(): array
    {
        return [
            __('report.headers.occupation'),
            __('report.headers.case_distribution'),
        ];
    }

    public function getSelectedFields(): array|string
    {
        return 'occupation';
    }

    public function addRelatedTables(): void
    {
        $this->query->join('beneficiary_details', 'beneficiaries.id', '=', 'beneficiary_details.beneficiary_id');
    }
}
