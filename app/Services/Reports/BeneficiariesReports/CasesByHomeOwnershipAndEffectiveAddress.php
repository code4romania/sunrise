<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Concerns\Reports\HasVerticalHeaderHomeOwnership;
use App\Enums\AddressType;
use App\Enums\ResidenceEnvironment;
use App\Interfaces\ReportGenerator;

class CasesByHomeOwnershipAndEffectiveAddress extends BaseGenerator implements ReportGenerator
{
    use HasVerticalHeaderHomeOwnership;

    public function getHorizontalHeader(): array
    {
        return [
            __('report.headers.home_ownership'),
            __('report.headers.cases_by_effective_address'),
            __('report.headers.subtotal'),
        ];
    }

    public function getHorizontalSubHeader(): ?array
    {
        return ResidenceEnvironment::options();
    }

    public function getHorizontalSubHeaderKey(): ?string
    {
        return 'environment';
    }

    public function getSelectedFields(): array|string
    {
        return ['homeownership', 'environment'];
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
