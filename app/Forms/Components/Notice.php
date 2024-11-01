<?php

declare(strict_types=1);

namespace App\Forms\Components;

use Filament\Forms\Components\Concerns\HasAffixes;
use Filament\Forms\Components\Concerns\HasKey;
use Filament\Forms\Components\Contracts\HasAffixActions;
use Filament\Forms\Components\Placeholder;
use Filament\Support\Concerns\HasColor;
use Filament\Support\Concerns\HasIcon;
use Filament\Support\Concerns\HasIconColor;

class Notice extends Placeholder implements HasAffixActions
{
    use HasIcon;
    use HasColor;
    use HasIconColor;
    use HasAffixes;
    use HasKey;

    protected string $view = 'forms.components.notice';
}
