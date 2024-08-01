<?php

declare(strict_types=1);

namespace App\Forms\Components;

use Filament\Forms\Components\Select as BaseSelect;

class Select extends BaseSelect
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->native(false);
    }
}
