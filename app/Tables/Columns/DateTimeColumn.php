<?php

declare(strict_types=1);

namespace App\Tables\Columns;

use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;

class DateTimeColumn extends TextColumn
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->formatStateUsing(fn (string|Carbon $state) => $state === '-' ? $state : $state->format('Y-m-d H:i:s'));
    }
}
