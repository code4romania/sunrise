<?php

declare(strict_types=1);

namespace App\Infolists\Components;

use Filament\Infolists\Components\Concerns;
use Filament\Infolists\Components\Entry;

class Notice extends Entry
{
    use Concerns\HasColor;
    use Concerns\HasFontFamily;
    use Concerns\HasIcon;
    use Concerns\HasIconColor;
    use Concerns\HasWeight;

    /**
     * @var view-string
     */
    protected string $view = 'components.notice';

    protected function setUp(): void
    {
        $this->hiddenLabel();
    }
}
