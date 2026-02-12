<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\LogsActivityOptions;
use App\Enums\MeetingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterventionMeeting extends Model
{
    use HasFactory;
    use LogsActivityOptions;

    protected $fillable = [
        'beneficiary_intervention_id',
        'specialist_id',
        'status',
        'date',
        'time',
        'duration',
        'topic',
        'observations',
    ];

    protected $casts = [
        'status' => MeetingStatus::class,
        'date' => 'date:Y-m-d',
        'time' => 'date:H:i',
    ];

    public function specialist(): BelongsTo
    {
        return $this->belongsTo(Specialist::class);
    }
}
