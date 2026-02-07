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

        $this->formatStateUsing(function (string|Carbon|null $state) {
            if ($state === null || $state === '' || $state === '-') {
                return '—';
            }

            if ($state instanceof Carbon) {
                return $state->format('d.m.Y H:i:s');
            }

            try {
                return Carbon::parse($state)->format('d.m.Y H:i:s');
            } catch (\Throwable) {
                return '—';
            }
        });
    }
}
