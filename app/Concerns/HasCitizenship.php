<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Enums\Citizenship;

trait HasCitizenship
{
    public function initializeHasCitizenship(): void
    {
        $this->fillable = array_merge($this->fillable, ['citizenship']);
        $this->casts['citizenship'] = Citizenship::class;
    }
}
