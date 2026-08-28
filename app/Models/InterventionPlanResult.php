<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToInterventionPlan;
use App\Concerns\LogsActivityOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterventionPlanResult extends Model
{
    use BelongsToInterventionPlan;
    use HasFactory;
    use LogsActivityOptions;

    protected $fillable = [
        'result_id',
        'user_id',
        'started_at',
        'ended_at',
        'retried_at',
        'retried',
        'lost_from_monitoring',
        'observations',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function result(): BelongsTo
    {
        return $this->belongsTo(Result::class);
    }

    public function activeResult(): BelongsTo
    {
        return $this->result()
            ->active();
    }
}
