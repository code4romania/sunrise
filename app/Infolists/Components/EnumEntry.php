<?php

declare(strict_types=1);

namespace App\Infolists\Components;

use BackedEnum;
use Filament\Infolists\Components\TextEntry;

class EnumEntry extends TextEntry
{
    protected function setUp(): void
    {
        $this->formatStateUsing(function ($state) {
            return $state instanceof BackedEnum ? $state->label() : $state;
        });
    }
}
