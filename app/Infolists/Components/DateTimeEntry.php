<?php

declare(strict_types=1);

namespace App\Infolists\Components;

use Carbon\Carbon;
use Filament\Infolists\Components\TextEntry;

class DateTimeEntry extends TextEntry
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->formatStateUsing(fn (string | Carbon $state) => $state === '-' ? $state : $state->format('d.m.Y H:i:s'));
    }
}
