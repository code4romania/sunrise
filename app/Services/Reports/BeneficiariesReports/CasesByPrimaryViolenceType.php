<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Concerns\Reports\HasVerticalHeaderViolence;
use App\Interfaces\ReportGenerator;

class CasesByPrimaryViolenceType extends BaseGenerator implements ReportGenerator
{
    use HasVerticalHeaderViolence;

    public function getHorizontalHeader(): array
    {
        return [
            __('report.headers.primary_violence'),
            __('report.headers.case_distribution'),
        ];
    }

    public function getVerticalHeaderKey(): string
    {
        return 'violence_primary_type';
    }

    public function getSelectedFields(): array|string
    {
        return 'violence_primary_type';
    }

    public function addRelatedTables(): void
    {
        $this->query->join('violences', 'violences.beneficiary_id', '=', 'beneficiaries.id');
    }
}
