<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Concerns\Reports\HasVerticalHeaderIncome;
use App\Enums\AddressType;
use App\Enums\Gender;
use App\Enums\ResidenceEnvironment;
use App\Interfaces\ReportGenerator;

class CasesByIncomeEffectiveAddressAndGender extends BaseGenerator implements ReportGenerator
{
    use HasVerticalHeaderIncome;

    public function getHorizontalHeader(): array
    {
        return [
            __('report.headers.income_and_effective_address'),
            __('report.headers.cases_by_gender'),
            __('report.headers.total'),
        ];
    }

    public function getHorizontalSubHeader(): ?array
    {
        return Gender::options();
    }

    public function getHorizontalSubHeaderKey(): ?string
    {
        return 'gender';
    }

    public function getVerticalSubHeader(): ?array
    {
        return ResidenceEnvironment::options();
    }

    public function getVerticalSubHeaderKey(): ?string
    {
        return 'environment';
    }

    public function getSelectedFields(): array|string
    {
        return ['income', 'gender', 'environment'];
    }

    public function addRelatedTables(): void
    {
        $this->query->join('beneficiary_details', 'beneficiaries.id', '=', 'beneficiary_details.beneficiary_id');
        $this->query->join('addresses', 'addresses.addressable_id', '=', 'beneficiaries.id');
    }

    public function addConditions(): void
    {
        parent::addConditions();
        $this->query->where('addresses.addressable_type', 'beneficiary')
            ->where('addresses.address_type', AddressType::EFFECTIVE_RESIDENCE);
    }
}
