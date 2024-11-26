<?php

declare(strict_types=1);

namespace App\Concerns\Reports;

use App\Enums\AddressType;

trait InteractWithEffectiveAddressAndBeneficiaryDetails
{
    public function addRelatedTables(): void
    {
        $this->query->join('beneficiary_details', 'beneficiary_details.beneficiary_id', '=', 'beneficiaries.id');
        $this->query->join('addresses', 'addresses.addressable_id', '=', 'beneficiaries.id');
    }

    public function addConditions(): void
    {
        parent::addConditions();
        $this->query->where('addresses.addressable_type', 'beneficiary')
            ->where('addresses.address_type', AddressType::EFFECTIVE_RESIDENCE);
    }
}