<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToBeneficiary;
use App\Concerns\LogsActivityOptions;
use App\Enums\AdmittanceReason;
use App\Enums\CloseMethod;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Znck\Eloquent\Relations\BelongsToThrough;
use Znck\Eloquent\Traits\BelongsToThrough as BelongsToThroughTrait;

class CloseFile extends Model
{
    use BelongsToBeneficiary;
    use BelongsToThroughTrait;
    use HasFactory;
    use LogsActivityOptions;

    protected $fillable = [
        'date',
        'admittance_date',
        'exit_date',
        'specialist_id',
        'admittance_reason',
        'admittance_details',
        'close_method',
        'institution_name',
        'beneficiary_request',
        'other_details',
        'close_situation',
        'confirm_closure_criteria',
        'confirm_documentation',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'admittance_date' => 'date',
            'exit_date' => 'date',
            'admittance_reason' => AsEnumCollection::class.':'.AdmittanceReason::class,
            'close_method' => CloseMethod::class,
            'confirm_closure_criteria' => 'boolean',
            'confirm_documentation' => 'boolean',
        ];
    }

    public function caseManager(): BelongsTo
    {
        return $this->belongsTo(Specialist::class, 'specialist_id');
    }

    public function organization(): BelongsToThrough
    {
        return $this->belongsToThrough(Organization::class, Beneficiary::class);
    }
}
