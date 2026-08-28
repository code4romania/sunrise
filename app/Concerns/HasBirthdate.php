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

    public function setBirthdateAttribute(mixed $value = null): void
    {
        if ($value === null || $value === '') {
            $this->attributes['birthdate'] = null;

            return;
        }

        if ($value instanceof \DateTimeInterface) {
            $this->attributes['birthdate'] = Carbon::instance($value)->format('Y-m-d');

            return;
        }

        $value = (string) $value;

        //        dd($value);
        $date = Carbon::createFromFormat('Y-m-d', $value);
        if ($date === false) {
            try {
                $date = Carbon::parse($value);
            } catch (\Throwable) {
                $this->attributes['birthdate'] = null;

                return;
            }
        }

        $this->attributes['birthdate'] = $date->format('Y-m-d');
    }
}
