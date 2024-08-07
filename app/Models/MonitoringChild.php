<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringChild extends Model
{
    use HasFactory;

    protected $fillable = [
        'monitoring_id',
        'name',
        'status',
        'age',
        'birthdate',
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
