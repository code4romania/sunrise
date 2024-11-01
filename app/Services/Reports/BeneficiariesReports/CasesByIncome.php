<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Concerns\Reports\HasVerticalHeaderIncome;
use App\Interfaces\ReportGenerator;

class CasesByIncome extends BaseGenerator implements ReportGenerator
{
    use HasVerticalHeaderIncome;

    public function getHorizontalHeader(): array
    {
        return [
            __('report.headers.income'),
            __('report.headers.case_distribution'),
        ];
    }

    public function getSelectedFields(): array|string
    {
        return 'income';
    }

    public function addRelatedTables(): void
    {
        $this->query->join('beneficiary_details', 'beneficiaries.id', '=', 'beneficiary_details.beneficiary_id');
    }
}
