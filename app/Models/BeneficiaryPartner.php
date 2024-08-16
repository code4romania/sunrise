<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToBeneficiary;
use App\Concerns\HasEffectiveAddress;
use App\Concerns\LogsActivityOptions;
use App\Enums\Occupation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class BeneficiaryPartner extends Model
{
    use HasFactory;
    use BelongsToBeneficiary;
    use HasEffectiveAddress;
    use LogsActivity;
    use LogsActivityOptions;

    protected $fillable = [
        'last_name',
        'first_name',
        'age',
        'occupation',
        'observations',
    ];

    protected $casts = [
        'occupation' => Occupation::class,
    ];
}
