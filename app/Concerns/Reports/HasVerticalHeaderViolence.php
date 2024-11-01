<?php

declare(strict_types=1);

namespace App\Concerns\Reports;

use App\Enums\Violence;

trait HasVerticalHeaderViolence
{
    public function getVerticalHeader(): array
    {
        return Violence::options();
    }

//    public function getVerticalHeaderKey(): string
//    {
//        return 'relationship';
//    }
}
