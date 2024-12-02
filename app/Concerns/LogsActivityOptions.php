<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

trait LogsActivityOptions
{
    use LogsActivity;

    protected static $recordEvents = ['created', 'updated', 'deleted', 'retrieved'];

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

    public function activity(): HasMany
    {
        return $this->hasMany(Activity::class, 'subject_id')
            ->where('subject_type', 'beneficiary');
    }
}
