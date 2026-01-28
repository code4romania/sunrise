<?php

declare(strict_types=1);

namespace App\Infolists\Components;

use Filament\Infolists\Components\Concerns\HasColor;
use Filament\Support\Concerns\HasFontFamily;
use Filament\Infolists\Components\Concerns\HasIcon;
use Filament\Infolists\Components\Concerns\HasIconColor;
use Filament\Support\Concerns\HasWeight;
use Filament\Infolists\Components\Concerns;
use Filament\Infolists\Components\Entry;

class Notice extends Entry
{
    use HasColor;
    use HasFontFamily;
    use HasIcon;
    use HasIconColor;
    use HasWeight;

    /**
     * @var view-string
     */
    protected string $view = 'components.notice';

    protected function setUp(): void
    {
        parent::setUp();

        $this->columnSpanFull();
    }
}
