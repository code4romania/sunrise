<?php

declare(strict_types=1);

namespace App\Forms\Components;

class Spacer extends \Filament\Schemas\Components\Component
{
    protected string $view = 'forms.components.spacer';

    public static function make(): static
    {
        return app(static::class)
            ->columnSpanFull();
    }
}
