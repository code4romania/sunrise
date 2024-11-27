<?php

declare(strict_types=1);

namespace App\Concerns;

use Cache;
use Filament\Support\Exceptions\Halt;

trait PreventMultipleSubmit
{
    public function beforeCreate(): void
    {
        $className = strtolower(str_ireplace(' ', '_', self::$resource::getTitleCaseModelLabel()));
        $cacheKey = \sprintf('create_%s_%d', $className, auth()->id());
        $lock = Cache::lock($cacheKey, 5);

        if (! $lock->get()) {
            throw new Halt();
        }
    }

    public function beforeSave(): void
    {
        $this->beforeCreate();
    }
}
