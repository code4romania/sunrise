<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonitoringSpecialist extends Model
{
    use HasFactory;

    protected $fillable = [
        'monitoring_id',
        'case_team_id',
    ];

    public function monitoring(): BelongsTo
    {
        return $this->belongsTo(Monitoring::class);
    }

    public function caseTeam(): BelongsTo
    {
        return $this->belongsTo(CaseTeam::class);
    }
}
