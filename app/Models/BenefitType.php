<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasGeneralStatus;
use App\Models\Scopes\SortOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BenefitType extends Model
{
    use HasFactory;
    use HasGeneralStatus;

    protected $fillable = [
        'benefit_id',
        'name',
        'sort',
    ];

    protected static function booted()
    {
        parent::booted();
        static::addGlobalScope(new SortOrder);
    }

    public function benefit(): BelongsTo
    {
        return $this->belongsTo(Benefit::class);
    }
}
