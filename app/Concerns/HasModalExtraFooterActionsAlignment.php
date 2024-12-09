<?php

declare(strict_types=1);

namespace App\Concerns;

use Closure;
use Filament\Support\Enums\Alignment;

trait HasModalExtraFooterActionsAlignment
{
    protected Alignment | string | Closure | null $modalExtraFooterActionsAlignment = null;

    public function getModalExtraFooterActionsAlignment(): string | Alignment | null
    {
        return $this->evaluate($this->modalExtraFooterActionsAlignment);
    }

    public function modalExtraFooterActionsAlignment(Alignment | string | Closure | null $modalExtraFooterActionsAlignment): self
    {
        $this->modalExtraFooterActionsAlignment = $modalExtraFooterActionsAlignment;

        return $this;
    }
}
