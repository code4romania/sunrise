<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Enums\Frequency;
use App\Interfaces\ReportGenerator;

class CasesByViolenceFrequency extends BaseGenerator implements ReportGenerator
{
    public function getHorizontalHeader(): array
    {
        return  [
            __('report.headers.frequency_violence'),
            __('report.headers.case_distribution'),
        ];
    }

    public function getVerticalHeader(): array
    {
        return Frequency::options();
    }

    public function getVerticalHeaderKey(): string
    {
        return 'frequency_violence';
    }

    public function getSelectedFields(): array|string
    {
        return 'frequency_violence';
    }

    public function addRelatedTables(): void
    {
        $this->query->join('violences', 'violences.beneficiary_id', '=', 'beneficiaries.id');
    }
}
