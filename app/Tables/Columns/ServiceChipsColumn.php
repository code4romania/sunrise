<?php

declare(strict_types=1);

namespace App\Tables\Columns;

use Closure;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\Concerns;

class ServiceChipsColumn extends Column
{
    use Concerns\HasColor;

    protected string $view = 'tables.columns.service-chips-column';

    protected string | Closure | null $size = null;

    public function size(string | Closure | null $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getSize(mixed $state): string | null
    {
        return $this->evaluate($this->size, [
            'state' => $state,
        ]);
    }
}
