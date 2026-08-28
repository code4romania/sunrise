<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Enums\Ternary;
use App\Interfaces\ReportGenerator;
use App\Models\RiskFactors;
use Illuminate\Support\Collection;

class CasesByEvaluationInitialRiskFactors extends BaseGenerator implements ReportGenerator
{
    public function getHorizontalHeader(): array
    {
        return [
            __('report.headers.risk_factor'),
            __('report.headers.case_distribution'),
        ];
    }

    public function getVerticalHeader(): array
    {
        return [
            'aggressor_present_risk_related_to_vices' => __('report.risk_factors.aggressor_present_risk_related_to_vices'),
            'aggressor_is_possessive_or_jealous' => __('report.risk_factors.aggressor_is_possessive_or_jealous'),
            'aggressor_have_mental_problems' => __('report.risk_factors.aggressor_have_mental_problems'),
            'aggressor_present_manifestations_of_economic_stress' => __('report.risk_factors.aggressor_present_manifestations_of_economic_stress'),
            'victim_afraid_for_himself' => __('report.risk_factors.victim_afraid_for_himself'),
            'victim_has_an_attitude_of_acceptance' => __('report.risk_factors.victim_has_an_attitude_of_acceptance'),
            'separation' => __('report.risk_factors.separation'),
            'aggressor_parent_has_contact_with_children' => __('report.risk_factors.aggressor_parent_has_contact_with_children'),
            'aggressor_parent_threaten_the_victim_in_the_visitation_program' => __('report.risk_factors.aggressor_parent_threaten_the_victim_in_the_visitation_program'),
            'children_from_other_marriage_are_integrated_into_family' => __('report.risk_factors.children_from_other_marriage_are_integrated_into_family'),
            'domestic_violence_during_pregnancy' => __('report.risk_factors.domestic_violence_during_pregnancy'),
        ];
    }

    public function getVerticalHeaderKey(): string
    {
        return 'factor_key';
    }

    public function getSelectedFields(): array|string
    {
        return 'beneficiaries.id';
    }

    public function getReportData(): Collection
    {
        $beneficiaryIds = $this->getFilteredBeneficiaryIds();
        $factorKeys = array_keys($this->getVerticalHeader());
        $rows = [];

        foreach ($factorKeys as $factorKey) {
            $count = RiskFactors::query()
                ->whereIn('beneficiary_id', $beneficiaryIds)
                ->whereRaw(
                    "JSON_UNQUOTE(JSON_EXTRACT(risk_factors, '$.\"{$factorKey}\".value')) = ?",
                    [(string) Ternary::YES->value]
                )
                ->distinct('beneficiary_id')
                ->count('beneficiary_id');

            $rows[] = (object) [
                'factor_key' => $factorKey,
                'total_cases' => $count,
            ];
        }

        return collect($rows);
    }

    private function getFilteredBeneficiaryIds(): Collection
    {
        $this->query = \App\Models\Beneficiary::query()->select('beneficiaries.id');
        $this->addConditions();

        return $this->query->pluck('beneficiaries.id');
    }
}
