<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasBirthdate;
use App\Enums\ChildAggressorRelationship;
use App\Enums\MaintenanceSources;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonitoringChild extends Model
{
    use HasFactory;
    use HasBirthdate;

    protected $fillable = [
        'monitoring_id',
        'name',
        'status',
        'age',
        'aggressor_relationship',
        'maintenance_sources',
        'location',
        'observations',
    ];

    protected $casts = [
        'aggressor_relationship' => ChildAggressorRelationship::class,
        'maintenance_sources' => MaintenanceSources::class,
    ];

    public function monitoring(): BelongsTo
    {
        return $this->belongsTo(Monitoring::class);
    }
}
