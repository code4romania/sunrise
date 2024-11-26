<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Enums\PresentationMode;
use App\Interfaces\ReportGenerator;

class CasesByPresentationMode extends BaseGenerator implements ReportGenerator
{
    public function getHorizontalHeader(): array
    {
        return [
            __('report.headers.presentation_mode'),
            __('report.headers.case_distribution'),
        ];
    }

    public function getVerticalHeader(): array
    {
        return PresentationMode::options();
    }

    public function getVerticalHeaderKey(): string
    {
        return 'presentation_mode';
    }

    public function getSelectedFields(): array|string
    {
        return 'presentation_mode';
    }

    public function addRelatedTables(): void
    {
        $this->query->join('flow_presentations', 'flow_presentations.beneficiary_id', '=', 'beneficiaries.id');
    }
}
