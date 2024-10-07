<?php

declare(strict_types=1);

namespace App\Tables\Filters;

use Filament\Tables\Filters\SelectFilter as BaseFilter;

class SelectFilter extends BaseFilter
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->native(false);
    }
}
