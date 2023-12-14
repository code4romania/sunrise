<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ServicePivot extends Pivot
{
    public $timestamps = false;

    protected $table = 'model_has_services';

    protected $fillable = [
        'model_type',
        'service_id',
        'is_visible',
        'is_available',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'is_available' => 'boolean',
    ];

    protected $with = [
        'service',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
