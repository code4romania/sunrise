<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Concerns\Reports\HasVerticalHeaderRelationship;
use App\Interfaces\ReportGenerator;

class CasesByAggressorRelationship extends BaseGenerator implements ReportGenerator
{
    use HasVerticalHeaderRelationship;

    public function getHorizontalHeader(): array
    {
        return [
            __('report.headers.aggressor_relationship'),
            __('report.headers.case_distribution'),
        ];
    }

    public function getSelectedFields(): array|string
    {
        return 'relationship';
    }

    public function addRelatedTables(): void
    {
        $this->query->join('aggressors', 'beneficiaries.id', '=', 'aggressors.beneficiary_id');
    }
}
