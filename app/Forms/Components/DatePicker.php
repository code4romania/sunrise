<?php

declare(strict_types=1);

namespace App\Forms\Components;

class DatePicker extends \Filament\Forms\Components\DatePicker
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->native(false);
        $this->displayFormat('Y-m-d');
    }
}
