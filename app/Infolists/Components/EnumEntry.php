<?php

declare(strict_types=1);

namespace App\Infolists\Components;

use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use ReflectionEnum;

class EnumEntry extends TextEntry
{
    protected ?string $enumClass = null;

    public function enumClass(?string $enumClass): static
    {
        $this->enumClass = $enumClass;

        return $this;
    }

    protected function setUp(): void
    {
        $this->formatStateUsing(function (mixed $state): mixed {
            if ($state === null || $state === '') {
                return null;
            }

            if ($state instanceof BackedEnum) {
                return method_exists($state, 'getLabel') ? $state->getLabel() : (string) $state->value;
            }

            if ($state === '-') {
                return $state;
            }

            if ($this->enumClass) {
                $stateCollection = collect(explode(',', (string) $state))->map(fn (string $item): string => trim($item))->filter();
                $reflectionClass = new ReflectionEnum($this->enumClass);
                $returnType = $reflectionClass->getBackingType();

                return $stateCollection->map(
                    function ($item) use ($returnType): ?string {
                        $converted = match ($returnType->getName()) {
                            'string' => trim((string) $item),
                            'int' => (int) $item,
                            default => $item,
                        };

                        $enum = $this->enumClass::tryFrom($converted);

                        return $enum !== null && method_exists($enum, 'getLabel') ? $enum->getLabel() : (string) $item;
                    }
                )->filter()->join(', ') ?: null;
            }

            return $state;
        });
    }
}
