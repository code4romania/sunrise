<?php

declare(strict_types=1);

namespace App\Concerns\Enums;

trait HasLabel
{
    protected function labelKeyPrefix(): ?string
    {
        return null;
    }

    /**
     * @deprecated Use `getLabel()` instead.
     */
    public function label(): ?string
    {
        return $this->getLabel();
    }

    public function getLabel(): ?string
    {
        $label = collect([$this->labelKeyPrefix(), $this->value])
            ->filter(fn ($value) => $value !== null)
            ->implode('.');

        return __($label);
    }
}
