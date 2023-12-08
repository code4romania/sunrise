<?php

declare(strict_types=1);

namespace App\Forms\Components;

use Filament\Forms\Components\Component;

class Spacer extends Component
{
    protected string $view = 'forms.components.spacer';

    public static function make(): static
    {
        return app(static::class)
            ->columnSpanFull();
    }
}
