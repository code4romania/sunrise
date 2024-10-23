<?php

declare(strict_types=1);

namespace App\Forms\Components;

use Closure;
use Filament\Forms\Components\Radio as BaseRadio;

class Radio extends BaseRadio
{
    protected string $view = 'forms.components.radio';

    protected bool | Closure $isLabelOptionsHidden = false;

    public function hiddenOptionLabel(bool | Closure $condition = true): static
    {
        $this->isLabelOptionsHidden = $condition;

        return $this;
    }

    public function isLabelOptionsHidden(): bool
    {
        return (bool) $this->evaluate($this->isLabelOptionsHidden);
    }
}
