<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Interfaces\ReportGenerator;
use App\Models\Result;
use Illuminate\Support\Facades\DB;

class CasesByResultsStatus extends BaseGenerator implements ReportGenerator
{
    public function getHorizontalHeader(): array
    {
        return [
            __('report.headers.intervention_result'),
            __('report.headers.results_by_status'),
            __('report.headers.subtotal'),
        ];
    }

    public function getHorizontalSubHeader(): ?array
    {
        return [
            'initiated' => __('report.headers.result_status_initiated'),
            'completed' => __('report.headers.result_status_completed'),
            'withdrawn' => __('report.headers.result_status_withdrawn'),
        ];
    }

    public function getVerticalHeader(): array
    {
        $header = Result::query()->active()->orderBy('sort')->pluck('name', 'id')->toArray();

        if (! $this->showMissingValues) {
            return $header;
        }

        $header[null] = __('report.headers.missing_values');

        return $header;
    }

    public function getHorizontalSubHeaderKey(): ?string
    {
        return 'status_bucket';
    }

    public function getVerticalHeaderKey(): string
    {
        return 'result_id';
    }

    public function getSelectedFields(): array|string
    {
        return [
            'intervention_plan_results.result_id',
            "CASE
                WHEN intervention_plan_results.retried = 1 THEN 'withdrawn'
                WHEN intervention_plan_results.ended_at IS NOT NULL THEN 'completed'
                WHEN intervention_plan_results.started_at IS NOT NULL THEN 'initiated'
                ELSE null
            END as status_bucket",
        ];
    }

    public function addRelatedTables(): void
    {
        $this->query
            ->join('intervention_plans', 'intervention_plans.beneficiary_id', '=', 'beneficiaries.id')
            ->join('intervention_plan_results', 'intervention_plan_results.intervention_plan_id', '=', 'intervention_plans.id');
    }

    public function addConditions(): void
    {
        parent::addConditions();

        if (! $this->startDate) {
            $this->query->whereRaw(
                'COALESCE(intervention_plan_results.retried_at, intervention_plan_results.ended_at, intervention_plan_results.started_at, intervention_plan_results.created_at) <= ?',
                [$this->endDate]
            );

            return;
        }

        $this->query->whereBetween(
            DB::raw('COALESCE(intervention_plan_results.retried_at, intervention_plan_results.ended_at, intervention_plan_results.started_at, intervention_plan_results.created_at)'),
            [$this->startDate, $this->endDate]
        );
    }
}
