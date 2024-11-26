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
        $header = ReferringInstitution::all()->pluck('name', 'id')->toArray();

        if (! $this->showMissingValues) {
            return $header;
        }

        $header[null] = __('report.headers.missing_values');

        return $header;
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
