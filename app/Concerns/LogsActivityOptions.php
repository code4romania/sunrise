<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Models\Activity;
use App\Models\Address;
use App\Models\Beneficiary;
use App\Models\BeneficiaryPartner;
use App\Models\Monitoring;
use App\Models\Specialist;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

trait LogsActivityOptions
{
    use LogsActivity;

    protected static $recordEvents = [
        'created',
        'updated',
        'deleted',
        //        'retrieved'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        if ($activity->subject instanceof Address) {
            if ($activity->subject->addressable instanceof Beneficiary) {
                $this->changeActivitySubject($activity, $activity->subject->addressable);

                return;
            }

            if ($activity->subject->addressable instanceof BeneficiaryPartner) {
                $activity->subject_type = 'beneficiary_partner_address';
                $this->changeActivitySubject($activity, $activity->subject->addressable->beneficiary);

                return;
            }
        }

        if ($activity->subject instanceof Specialist) {
            if ($activity->subject->specialistable instanceof Beneficiary) {
                $this->changeActivitySubject($activity, $activity->subject->specialistable);

                return;
            }

            if ($activity->subject->specialistable instanceof Monitoring) {
                $activity->subject_type = 'monitoring_specialist';
                $this->changeActivitySubject($activity, $activity->subject->specialistable->beneficiary);

                return;
            }
        }

        if ($activity->subject instanceof Beneficiary ||
            ! method_exists($activity->subject, 'beneficiary')) {
            return;
        }

        $this->loadMissing('beneficiary');
        $this->changeActivitySubject($activity, $this->beneficiary);
    }

    public function activity(): HasMany
    {
        return $this->hasMany(Activity::class, 'subject_id')
            ->where('subject_type', 'beneficiary');
    }

    public function changeActivitySubject(Activity $activity, Model $model): void
    {
        $activity->event = $activity->subject_type;
        $activity->subject_type = $model->getMorphClass();

        $activity->subject()->associate($model);
    }
}
