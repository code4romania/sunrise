<?php

declare(strict_types=1);

namespace App\Concerns\Enums;

trait HasLabel
{
    protected function labelKeyPrefix(): ?string
    {
        return 'vulnerability.label';
    }

    public function label(): string
    {
        $label = collect([$this->labelKeyPrefix(), $this->value])
            ->filter(fn ($value) => $value !== null)
            ->implode('.');

        return __($label);
    }

    public function getLabel(): string
    {
        return $this->label();
    }
}
