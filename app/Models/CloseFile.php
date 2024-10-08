<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToBeneficiary;
use App\Enums\AdmittanceReason;
use App\Enums\CloseMethod;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CloseFile extends Model
{
    use HasFactory;
    use BelongsToBeneficiary;

    protected $fillable = [
        'date',
        'number',
        'admittance_date',
        'exit_date',
        'case_team_member_id',
        'admittance_reason',
        'admittance_details',
        'close_method',
        'institution_name',
        'beneficiary_request',
        'other_details',
        'close_situation',
    ];

    protected $casts = [
        'admittance_reason' => AsEnumCollection::class . ':' . AdmittanceReason::class,
        'close_method' => CloseMethod::class,
    ];

    public function caseManager(): BelongsTo
    {
        return $this->belongsTo(CaseTeamMember::class);
    }
}
