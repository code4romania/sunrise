<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Enums\BeneficiarySegmentationByAge;
use App\Interfaces\ReportGenerator;
use App\Models\Service;
use Illuminate\Database\Eloquent\Builder;

class CasesByServiceTypesAndAge extends BaseGenerator implements ReportGenerator
{
    public function getHorizontalHeader(): array
    {
        return [
            __('report.headers.service_type'),
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
        $header = Service::query()->active()->orderBy('sort')->pluck('name', 'id')->toArray();

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
        return 'service_id';
    }

    public function getSelectedFields(): array|string
    {
        return [
            'monthly_plan_services.service_id',
            "CASE
                WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) < 18 THEN 'minor'
                WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) >= 18 THEN 'major'
                ELSE 'unknown'
            END as age_group",
        ];
    }

    public function addRelatedTables(): void
    {
        $this->query
            ->join('intervention_plans', 'intervention_plans.beneficiary_id', '=', 'beneficiaries.id')
            ->join('monthly_plans', 'monthly_plans.intervention_plan_id', '=', 'intervention_plans.id')
            ->join('monthly_plan_services', 'monthly_plan_services.monthly_plan_id', '=', 'monthly_plans.id');
    }

    public function addConditions(): void
    {
        parent::addConditions();

        $this->query->where(function (Builder $query): void {
            if ($this->startDate) {
                $query->whereDate('monthly_plans.end_date', '>=', $this->startDate);
            }

            $query->whereDate('monthly_plans.start_date', '<=', $this->endDate);
        });
    }
}
