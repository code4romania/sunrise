<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Interfaces\ReportGenerator;
use App\Models\ReferringInstitution;

class CasesByReferringInstitution extends BaseGenerator implements ReportGenerator
{
    public function getHorizontalHeader(): array
    {
        return [
            __('report.headers.referring_institution'),
            __('report.headers.case_distribution'),
        ];
    }

    public function getVerticalHeader(): array
    {
        return ReferringInstitution::all()->pluck('name', 'id')->toArray();
    }

    public function getVerticalHeaderKey(): string
    {
        return 'referring_institution_id';
    }

    public function getSelectedFields(): array|string
    {
        return 'referring_institution_id';
    }

    public function addRelatedTables(): void
    {
        $this->query->join('flow_presentations', 'flow_presentations.beneficiary_id', '=', 'beneficiaries.id');
    }
}
