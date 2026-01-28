<?php

declare(strict_types=1);

namespace App\Infolists\Components;

use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use ReflectionEnum;

class EnumEntry extends TextEntry
{
    protected string | null $enumClass = null;

    public function enumClass(string | null $enumClass): static
    {
        $this->enumClass = $enumClass;

        return $this;
    }

    protected function setUp(): void
    {
        $this->formatStateUsing(function ($state) {
            if ($state instanceof BackedEnum) {
                return $state->getLabel();
            }

            if ($state === '-') {
                return $state;
            }

            if ($this->enumClass) {
                $state = collect(explode(',', $state));
                $reflectionClass = new ReflectionEnum($this->enumClass);
                $returnType = $reflectionClass->getBackingtype();

                return $state->map(
                    function ($item) use ($returnType) {
                        $item = match ($returnType->getName()) {
                            'string' => trim((string) $item),
                            'int' => (int) $item,
                        };

                        return $this->enumClass::tryFrom($item)?->getLabel();
                    }
                )->join(', ');
            }

            return $state;
        });
    }
}
