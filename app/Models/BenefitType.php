<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\GeneralStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BenefitType extends Model
{
    use HasFactory;

    protected $fillable = [
        'benefit_id',
        'name',
        'status',
    ];

    protected $casts = [
        'status' => GeneralStatus::class,
    ];

    public function benefit(): BelongsTo
    {
        return $this->belongsTo(Benefit::class);
    }
}
