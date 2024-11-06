<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceCounselingSheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'intervention_service_id',
        'data',
    ];

    protected $casts = [
        'data' => 'json',
    ];

    public function interventionService(): BelongsTo
    {
        return $this->belongsTo(InterventionService::class);
    }
}
