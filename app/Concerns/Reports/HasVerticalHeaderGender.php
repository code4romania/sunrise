<?php

declare(strict_types=1);

namespace App\Concerns\Reports;

use App\Enums\Gender;

trait HasVerticalHeaderGender
{
    public function getVerticalHeader(): array
    {
        return Gender::options();
    }

    public function getVerticalHeaderKey(): string
    {
        return 'gender';
    }
}
