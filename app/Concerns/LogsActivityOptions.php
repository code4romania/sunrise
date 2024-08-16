<?php

declare(strict_types=1);

namespace App\Concerns;

use Spatie\Activitylog\LogOptions;

trait LogsActivityOptions
{
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
