<?php

declare(strict_types=1);

namespace App\Infolists\Components;

use Filament\Infolists\Components\Concerns;
use Filament\Infolists\Components\Entry;

class SectionHeader extends Entry
{
    use Concerns\HasFontFamily;
    use Concerns\HasWeight;

    protected $badge;

    /**
     * @var view-string
     */
    protected string $view = 'components.section-header';

    public function badge(\Closure $param)
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
