<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToInterventionPlan;
use App\Concerns\LogsActivityOptions;
use App\Enums\AwardMethod;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BenefitService extends Model
{
    use HasFactory;
    use LogsActivityOptions;
    use BelongsToInterventionPlan;

    protected $fillable = [
        'benefit_id',
        'benefit_types',
        'award_methods',
        'description',
    ];

    protected $casts = [
        'benefit_types' => 'json',
        'award_methods' => AsEnumCollection::class . ':' . AwardMethod::class,
    ];

    public function benefit(): BelongsTo
    {
        return $this->belongsTo(Benefit::class)
            ->active();
    }
}
