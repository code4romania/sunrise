<?php

declare(strict_types=1);

namespace App\Forms\Components;

use Carbon\Carbon;
use Closure;
use Filament\Forms\Components\TextInput;

class DateInput extends TextInput
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->mask('99.99.9999');
        $this->formatStateUsing(fn (?string $state) => $state ? Carbon::parse($state)->format('d.m.Y') : null);
        $this->rules([
            'date_format:d.m.Y',
            fn (): Closure => function (string $attribute, $value, Closure $fail) {
                if (Carbon::createFromFormat('d.m.Y', $value)->greaterThan(now())) {
                    $fail(__('validation.before_or_equal', [
                        'date' => now()->format('d.m.Y'),
                    ]));
                }
            },
        ]);
        $this->placeholder(__('general.placeholders.date'));
    }
}
