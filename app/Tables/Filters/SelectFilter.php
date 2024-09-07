<?php

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
