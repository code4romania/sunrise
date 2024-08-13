<?php

declare(strict_types=1);

namespace App\Concerns\Enums;

trait HasLabel
{
    protected function labelKeyPrefix(): ?string
    {
        return 'vulnerability.label';
    }

    /* @deprecated use getLabel() */
    public function label()
    {
        return $this->getLabel();
    }

    public function getLabel(): string
    {
        $label = collect([$this->labelKeyPrefix(), $this->value])
            ->filter(fn (?string $value) => $value !== null)
            ->implode('.');

        return __($label);
    }
}
