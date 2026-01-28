<?php

declare(strict_types=1);

namespace App\Infolists\Components;

use Closure;
use Filament\Infolists\Components\Entry;
use Filament\Support\Concerns\HasFontFamily;
use Filament\Support\Concerns\HasWeight;

class SectionHeader extends Entry
{
    use HasFontFamily;
    use HasWeight;

    protected $badge;

    /**
     * @var view-string
     */
    protected string $view = 'components.section-header';

    public function badge(Closure $param)
    {
        $this->badge = $param;

        return $this;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->columnSpanFull();
    }

    public function getBadge()
    {
        return $this->evaluate($this->badge);
    }
}
