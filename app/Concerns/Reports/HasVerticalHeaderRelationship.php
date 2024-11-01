<?php

declare(strict_types=1);

namespace App\Concerns\Reports;

use App\Enums\AggressorRelationship;

trait HasVerticalHeaderRelationship
{
    public function getVerticalHeader(): array
    {
        return AggressorRelationship::options();
    }

    public function getVerticalHeaderKey(): string
    {
        return 'relationship';
    }
}
