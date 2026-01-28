<?php

declare(strict_types=1);

namespace App\Forms\Components;

use Filament\Forms\Components\Concerns\HasAffixes;
use Filament\Forms\Components\Placeholder;

class Notice extends Placeholder implements \Filament\Schemas\Components\Contracts\HasAffixActions
{
    // HasIcon, HasColor, HasIconColor are already available from TextEntry (via Placeholder)
    use HasAffixes;
    use \Filament\Schemas\Components\Concerns\HasKey;

    protected string $view = 'forms.components.notice';
}
