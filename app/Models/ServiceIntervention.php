<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\GeneralStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceIntervention extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'name',
        'status',
    ];

    protected $casts = [
        'status' => GeneralStatus::class,
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
