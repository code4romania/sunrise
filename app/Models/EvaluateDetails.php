<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToBeneficiary;
use App\Concerns\LogsActivityOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class EvaluateDetails extends Model
{
    use BelongsToBeneficiary;
    use HasFactory;
    use LogsActivityOptions;

    protected $fillable = [
        'specialist_id',
        'registered_date',
        'file_number',
        'method_of_identifying_the_service',
    ];

    protected $casts = [
        'registered_date' => 'date',
    ];

    public function organization(): HasOneThrough
    {
        return $this->hasOneThrough(
            Organization::class,
            Beneficiary::class,
            'id',              // FK on Beneficiary (referenced by evaluate_details.beneficiary_id)
            'id',              // FK on Organization (referenced by beneficiaries.organization_id)
            'beneficiary_id',  // Local key on EvaluateDetails
            'organization_id' // Local key on Beneficiary
        );
    }

    public function specialist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'specialist_id');
    }
}
