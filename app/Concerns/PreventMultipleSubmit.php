<?php

declare(strict_types=1);

namespace App\Concerns;

use Cache;
use Filament\Support\Exceptions\Halt;
use Illuminate\Contracts\Cache\Lock;
use Illuminate\Support\Str;

trait PreventMultipleSubmit
{
    public function beforeCreate(): void
    {
        $className = Str::replace(' ', '_', self::$resource::getTitleCaseModelLabel());
        $className = Str::lower($className);
        $cacheKey = \sprintf('create_%s_%d', $className, auth()->id());
        $lock = Cache::lock($cacheKey, 5);

        if (! $lock->get()) {
            throw new Halt;
        }

        $this->deferReleasingPreventMultipleSubmitLock($lock);
    }

    public function beforeSave(): void
    {
        $this->beforeCreate();
    }

    private function deferReleasingPreventMultipleSubmitLock(Lock $lock): void
    {
        defer(function () use ($lock): void {
            $lock->release();
        });
    }
}
