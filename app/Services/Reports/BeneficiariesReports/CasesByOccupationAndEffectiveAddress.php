<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Concerns\Reports\HasVerticalHeaderOccupation;
use App\Enums\AddressType;
use App\Enums\ResidenceEnvironment;
use App\Interfaces\ReportGenerator;

class CasesByOccupationAndEffectiveAddress extends BaseGenerator implements ReportGenerator
{
    use HasVerticalHeaderOccupation;

    public function getHorizontalHeader(): array
    {
        return [
            __('report.headers.occupation'),
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
        return ['occupation', 'environment'];
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
