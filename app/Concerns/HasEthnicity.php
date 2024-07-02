<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Enums\Ethnicity;

trait HasEthnicity
{
    public function initializeHasEthnicity(): void
    {
        $this->fillable = array_merge($this->fillable, ['ethnicity']);
        $this->casts['ethnicity'] = Ethnicity::class;
    }
}
