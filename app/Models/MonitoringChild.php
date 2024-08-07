<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ChildAggressorRelationship;
use App\Enums\MaintenanceSources;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringChild extends Model
{
    use HasFactory;

    protected $fillable = [
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
}
