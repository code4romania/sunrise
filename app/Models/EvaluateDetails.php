<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToBeneficiary;
use App\Concerns\LogsActivityOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluateDetails extends Model
{
    use BelongsToBeneficiary;
    use HasFactory;
    use LogsActivityOptions;

    protected $fillable = [
        'beneficiary_id',
        'organization_id',
        'specialist_id',
        'registered_date',
        'file_number',
        'method_of_identifying_the_service',
    ];

    protected $casts = [
        'registered_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (EvaluateDetails $model): void {
            if ($model->organization_id === null && $model->beneficiary_id !== null) {
                $model->organization_id = Beneficiary::find($model->beneficiary_id)?->organization_id;
            }
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function specialist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'specialist_id');
    }
}
