<?php

declare(strict_types=1);

namespace App\Concerns\Reports;

trait InteractWithViolence
{
    public function addRelatedTables(): void
    {
        $this->query->join('violences', 'violences.beneficiary_id', '=', 'beneficiaries.id');
    }
}
