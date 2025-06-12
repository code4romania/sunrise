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
        $date = $value ? Carbon::parse($value)->format('Y-m-d') : null;

        $this->attributes['birthdate'] = $date;
    }
}
