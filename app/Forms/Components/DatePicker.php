<?php

declare(strict_types=1);

namespace App\Forms\Components;

use Filament\Forms\Components\DatePicker as BaseDatePicker;

class DatePicker extends BaseDatePicker
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->displayFormat('Y-m-d');
        $this->format('Y-m-d');

        $this->closeOnDateSelection();

        $this->placeholder(__('general.placeholders.date'));
    }
}
