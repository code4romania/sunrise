<?php

declare(strict_types=1);

namespace App\Concerns\Reports;

use App\Enums\AddressType;

trait InteractWithLegalAddress
{
    public function addRelatedTables(): void
    {
        $this->query->join('addresses', 'addresses.addressable_id', '=', 'beneficiaries.id');
    }

    public function addConditions(): void
    {
        parent::addConditions();
        $this->query->where('addresses.addressable_type', 'beneficiary')
            ->where('addresses.address_type', AddressType::LEGAL_RESIDENCE);
    }
}
