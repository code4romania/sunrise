<?php

declare(strict_types=1);

namespace App\Concerns;

use Carbon\Carbon;

trait HasBirthdate
{
    public function initializeHasBirthdate()
    {
        $this->casts['birthdate'] = 'date';

        $this->fillable[] = 'birthdate';
    }

    public function setBirthdateAttribute(?string $value = null): void
    {
        $date = $value ? Carbon::createFromFormat('d-m-Y', $value) : null;

        $this->attributes['birthdate'] = $date?->format('Y-m-d');
    }
}
