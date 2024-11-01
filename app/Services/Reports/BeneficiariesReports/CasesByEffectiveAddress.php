<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Concerns\Reports\HasVerticalHeaderEnvironment;
use App\Enums\AddressType;
use App\Interfaces\ReportGenerator;

class CasesByEffectiveAddress extends BaseGenerator implements ReportGenerator
{
    use HasVerticalHeaderEnvironment;

    public function getHorizontalHeader(): array
    {
        return [
            __('report.headers.effective_address'),
            __('report.headers.case_distribution'),
        ];
    }

    public function getSelectedFields(): array|string
    {
        return 'environment';
    }

    public function addRelatedTables(): void
    {
        $this->query->join('addresses', 'addresses.addressable_id', '=', 'beneficiaries.id');
    }

    public function addConditions(): void
    {
        parent::addConditions();
        $this->query->where('addresses.addressable_type', 'beneficiary')
            ->where('addresses.address_type', AddressType::EFFECTIVE_RESIDENCE);
    }
}
