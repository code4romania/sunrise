<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Enums\AggressorLegalHistory;
use App\Enums\Drug;
use App\Enums\Ternary;
use App\Interfaces\ReportGenerator;
use App\Models\Aggressor;
use Illuminate\Support\Collection;

class CasesByAggressorRiskFactors extends BaseGenerator implements ReportGenerator
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
            'aggressor_has_violence_history' => __('report.risk_factors.aggressor_has_violence_history'),
            'aggressor_criminal_history' => __('report.risk_factors.aggressor_criminal_history'),
            'aggressor_has_police_reports' => __('report.risk_factors.aggressor_has_police_reports'),
            'aggressor_has_medical_reports' => __('report.risk_factors.aggressor_has_medical_reports'),
            'aggressor_legal_history_crimes' => __('report.risk_factors.aggressor_legal_history_crimes'),
            'aggressor_legal_history_contraventions' => __('report.risk_factors.aggressor_legal_history_contraventions'),
            'aggressor_legal_history_protection_order' => __('report.risk_factors.aggressor_legal_history_protection_order'),
            'aggressor_electronic_monitoring' => __('report.risk_factors.aggressor_electronic_monitoring'),
            'aggressor_psychiatric_history' => __('report.risk_factors.aggressor_psychiatric_history'),
            'aggressor_alcohol_frequent' => __('report.risk_factors.aggressor_alcohol_frequent'),
            'aggressor_drug_use' => __('report.risk_factors.aggressor_drug_use'),
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

        $counts = [
            'aggressor_has_violence_history' => Aggressor::query()
                ->whereIn('beneficiary_id', $beneficiaryIds)
                ->where('has_violence_history', Ternary::YES->value)
                ->distinct('beneficiary_id')
                ->count('beneficiary_id'),
            'aggressor_criminal_history' => Aggressor::query()
                ->whereIn('beneficiary_id', $beneficiaryIds)
                ->whereJsonContains('legal_history', AggressorLegalHistory::CRIMES->value)
                ->distinct('beneficiary_id')
                ->count('beneficiary_id'),
            'aggressor_has_police_reports' => Aggressor::query()
                ->whereIn('beneficiary_id', $beneficiaryIds)
                ->where('has_police_reports', Ternary::YES->value)
                ->distinct('beneficiary_id')
                ->count('beneficiary_id'),
            'aggressor_has_medical_reports' => Aggressor::query()
                ->whereIn('beneficiary_id', $beneficiaryIds)
                ->where('has_medical_reports', Ternary::YES->value)
                ->distinct('beneficiary_id')
                ->count('beneficiary_id'),
            'aggressor_legal_history_crimes' => Aggressor::query()
                ->whereIn('beneficiary_id', $beneficiaryIds)
                ->whereJsonContains('legal_history', AggressorLegalHistory::CRIMES->value)
                ->distinct('beneficiary_id')
                ->count('beneficiary_id'),
            'aggressor_legal_history_contraventions' => Aggressor::query()
                ->whereIn('beneficiary_id', $beneficiaryIds)
                ->whereJsonContains('legal_history', AggressorLegalHistory::CONTRAVENTIONS->value)
                ->distinct('beneficiary_id')
                ->count('beneficiary_id'),
            'aggressor_legal_history_protection_order' => Aggressor::query()
                ->whereIn('beneficiary_id', $beneficiaryIds)
                ->whereJsonContains('legal_history', AggressorLegalHistory::PROTECTION_ORDER->value)
                ->distinct('beneficiary_id')
                ->count('beneficiary_id'),
            'aggressor_electronic_monitoring' => Aggressor::query()
                ->whereIn('beneficiary_id', $beneficiaryIds)
                ->where('electronically_monitored', Ternary::YES->value)
                ->distinct('beneficiary_id')
                ->count('beneficiary_id'),
            'aggressor_psychiatric_history' => Aggressor::query()
                ->whereIn('beneficiary_id', $beneficiaryIds)
                ->where('has_psychiatric_history', Ternary::YES->value)
                ->distinct('beneficiary_id')
                ->count('beneficiary_id'),
            'aggressor_alcohol_frequent' => Aggressor::query()
                ->whereIn('beneficiary_id', $beneficiaryIds)
                ->where('has_drug_history', Ternary::YES->value)
                ->whereJsonContains('drugs', Drug::ALCOHOL_FREQUENT->value)
                ->distinct('beneficiary_id')
                ->count('beneficiary_id'),
            'aggressor_drug_use' => Aggressor::query()
                ->whereIn('beneficiary_id', $beneficiaryIds)
                ->where('has_drug_history', Ternary::YES->value)
                ->whereJsonContains('drugs', Drug::DRUGS->value)
                ->distinct('beneficiary_id')
                ->count('beneficiary_id'),
        ];

        return collect($counts)
            ->map(fn (int $totalCases, string $factorKey): object => (object) [
                'factor_key' => $factorKey,
                'total_cases' => $totalCases,
            ])
            ->values();
    }

    private function getFilteredBeneficiaryIds(): Collection
    {
        $this->query = \App\Models\Beneficiary::query()->select('beneficiaries.id');
        $this->addConditions();

        return $this->query->pluck('beneficiaries.id');
    }
}
