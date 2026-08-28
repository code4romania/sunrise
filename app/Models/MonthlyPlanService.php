<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\LogsActivityOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Znck\Eloquent\Relations\BelongsToThrough;
use Znck\Eloquent\Traits\BelongsToThrough as BelongsToThroughTrait;

class MonthlyPlanService extends Model
{
    use BelongsToThroughTrait;
    use HasFactory;
    use LogsActivityOptions;

    protected $fillable = [
        'monthly_plan_id',
        'service_id',
        'institution',
        'responsible_person',
        'start_date',
        'end_date',
        'objective',
        'service_details',
    ];

    protected static function booted(): void
    {
        static::deleting(function (self $service): void {
            $service->monthlyPlanInterventions()->delete();
        });
    }

    public function monthlyPlan(): BelongsTo
    {
        return $this->belongsTo(MonthlyPlan::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function monthlyPlanInterventions(): HasMany
    {
        return $this->hasMany(MonthlyPlanInterventions::class)->orderBy('id');
    }

    public function beneficiary(): BelongsToThrough
    {
        return $this->belongsToThrough(Beneficiary::class, [
            InterventionPlan::class,
            MonthlyPlan::class,
        ]);
    }
}
