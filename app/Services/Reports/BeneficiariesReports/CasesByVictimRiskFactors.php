<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Enums\Diseases;
use App\Enums\Drug;
use App\Enums\Ternary;
use App\Interfaces\ReportGenerator;
use App\Models\BeneficiaryDetails;
use Illuminate\Support\Collection;

class CasesByVictimRiskFactors extends BaseGenerator implements ReportGenerator
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
            'victim_health_chronic' => __('report.risk_factors.victim_health_chronic'),
            'victim_health_degenerative' => __('report.risk_factors.victim_health_degenerative'),
            'victim_health_mental' => __('report.risk_factors.victim_health_mental'),
            'victim_disabilities' => __('report.risk_factors.victim_disabilities'),
            'victim_psychiatric_history' => __('report.risk_factors.victim_psychiatric_history'),
            'victim_alcohol_frequent' => __('report.risk_factors.victim_alcohol_frequent'),
            'victim_drug_use' => __('report.risk_factors.victim_drug_use'),
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
            'victim_health_chronic' => BeneficiaryDetails::query()
                ->whereIn('beneficiary_id', $beneficiaryIds)
                ->whereJsonContains('health_status', Diseases::CHRONIC_DISEASES->value)
                ->distinct('beneficiary_id')
                ->count('beneficiary_id'),
            'victim_health_degenerative' => BeneficiaryDetails::query()
                ->whereIn('beneficiary_id', $beneficiaryIds)
                ->whereJsonContains('health_status', Diseases::DEGENERATIVE_DISEASES->value)
                ->distinct('beneficiary_id')
                ->count('beneficiary_id'),
            'victim_health_mental' => BeneficiaryDetails::query()
                ->whereIn('beneficiary_id', $beneficiaryIds)
                ->whereJsonContains('health_status', Diseases::MENTAL_ILLNESSES->value)
                ->distinct('beneficiary_id')
                ->count('beneficiary_id'),
            'victim_disabilities' => BeneficiaryDetails::query()
                ->whereIn('beneficiary_id', $beneficiaryIds)
                ->where('disabilities', Ternary::YES->value)
                ->distinct('beneficiary_id')
                ->count('beneficiary_id'),
            'victim_psychiatric_history' => BeneficiaryDetails::query()
                ->whereIn('beneficiary_id', $beneficiaryIds)
                ->where('psychiatric_history', Ternary::YES->value)
                ->distinct('beneficiary_id')
                ->count('beneficiary_id'),
            'victim_alcohol_frequent' => BeneficiaryDetails::query()
                ->whereIn('beneficiary_id', $beneficiaryIds)
                ->where('drug_consumption', Ternary::YES->value)
                ->whereJsonContains('drug_types', Drug::ALCOHOL_FREQUENT->value)
                ->distinct('beneficiary_id')
                ->count('beneficiary_id'),
            'victim_drug_use' => BeneficiaryDetails::query()
                ->whereIn('beneficiary_id', $beneficiaryIds)
                ->where('drug_consumption', Ternary::YES->value)
                ->whereJsonContains('drug_types', Drug::DRUGS->value)
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
