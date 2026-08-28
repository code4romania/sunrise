<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Enums\BeneficiarySegmentationByAge;
use App\Enums\Violence;
use App\Interfaces\ReportGenerator;
use Illuminate\Support\Collection;

class CasesByViolenceTypesAndAge extends BaseGenerator implements ReportGenerator
{
    private Collection $reportData;

    public function getHorizontalHeader(): array
    {
        return [
            __('report.headers.violence_types'),
            __('report.headers.cases_by_age_segmentation'),
            __('report.headers.subtotal'),
        ];
    }

    public function getHorizontalSubHeader(): ?array
    {
        $header = BeneficiarySegmentationByAge::options();

        if (! $this->showMissingValues) {
            return $header;
        }

        $header['unknown'] = __('report.headers.missing_values');

        return $header;
    }

    public function getVerticalHeader(): array
    {
        $header = Violence::options();

        if (! $this->showMissingValues) {
            return $header;
        }

        $header[null] = __('report.headers.missing_values');

        return $header;
    }

    public function getHorizontalSubHeaderKey(): ?string
    {
        return 'age_group';
    }

    public function getVerticalHeaderKey(): string
    {
        return 'violence_types';
    }

    public function getSelectedFields(): array|string
    {
        return [
            'JSON_UNQUOTE(JSON_EXTRACT(violences.violence_types, "$[*]")) as violence_types',
            "CASE
                WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) < 18 THEN 'minor'
                WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) >= 18 THEN 'major'
                ELSE 'unknown'
            END as age_group",
        ];
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
            $violenceTypes = $row->violence_types ? json_decode($row->violence_types, true) : [];

            if (empty($violenceTypes) && $this->showMissingValues) {
                $this->addCasesForViolenceType($row, null);
            }

            foreach ($violenceTypes as $violenceType) {
                $this->addCasesForViolenceType($row, $violenceType);
            }
        }

        return $this->reportData;
    }

    private function addCasesForViolenceType(object $row, ?string $violenceType): void
    {
        $element = $this->reportData
            ->first(fn (object $item): bool => $item->violence_types === $violenceType && $item->age_group === $row->age_group);

        if ($element) {
            $element->total_cases += $row->total_cases;

            return;
        }

        $element = clone $row;
        $element->violence_types = $violenceType;
        $this->reportData->add($element);
    }
}
