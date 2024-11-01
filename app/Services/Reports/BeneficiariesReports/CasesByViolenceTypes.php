<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Concerns\Reports\HasVerticalHeaderViolence;
use App\Interfaces\ReportGenerator;
use Illuminate\Support\Collection;

class CasesByViolenceTypes extends BaseGenerator implements ReportGenerator
{
    use HasVerticalHeaderViolence;

    private Collection $reportData;

    public function getHorizontalHeader(): array
    {
        return [
            __('report.headers.violence_types'),
            __('report.headers.case_distribution'),
        ];
    }

    public function getVerticalHeaderKey(): string
    {
        return 'violence_types';
    }

    public function getSelectedFields(): array|string
    {
        return 'JSON_UNQUOTE(JSON_EXTRACT(violences.violence_types, "$[*]")) as violence_types';
    }

    public function addRelatedTables(): void
    {
        $this->query->join('violences', 'violences.beneficiary_id', '=', 'beneficiaries.id');
    }

    public function getReportData(): Collection
    {
        $data = parent::getReportData();
        $this->reportData = collect();

        foreach ($data as $row) {
            $violenceTypes = $row->violence_types ? json_decode($row->violence_types) : [];
            if (empty($violenceTypes) && $this->showMissingValues) {
                $this->addCasesForViolenceTypes($row);
            }
            foreach ($violenceTypes as $violenceType) {
                $this->addCasesForViolenceTypes($row, $violenceType);
            }
        }

        return $this->reportData;
    }

    private function addCasesForViolenceTypes($row, ?string $violenceType = null): void
    {
        $element = $this->reportData->filter(fn ($item) => $item->violence_types === $violenceType)->first();
        if ($element) {
            $element->total_cases += $row->total_cases;

            return;
        }
        $element = clone $row;
        $element->violence_types = $violenceType;
        $this->reportData->add($element);
    }
}
