<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Concerns\Reports\HasVerticalHeaderHomeOwnership;
use App\Interfaces\ReportGenerator;

class CasesByHomeOwnership extends BaseGenerator implements ReportGenerator
{
    use HasVerticalHeaderHomeOwnership;

    public function getHorizontalHeader(): array
    {
        return [
            __('report.headers.home_ownership'),
            __('report.headers.case_distribution'),
        ];
    }

    public function getSelectedFields(): array|string
    {
        return 'homeownership';
    }

    public function addRelatedTables(): void
    {
        $this->query->join('beneficiary_details', 'beneficiaries.id', '=', 'beneficiary_details.beneficiary_id');
    }
}
