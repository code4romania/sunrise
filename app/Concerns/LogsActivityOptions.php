<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Models\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

trait LogsActivityOptions
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        if ($activity->subject_type === 'beneficiary') {
            return;
        }

        $activity->event = $activity->subject_type;
        $activity->subject()->associate($this->beneficiary);
    }
}
