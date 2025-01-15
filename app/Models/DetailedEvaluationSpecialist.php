<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToBeneficiary;
use App\Concerns\LogsActivityOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailedEvaluationSpecialist extends Model
{
    use HasFactory;
    use BelongsToBeneficiary;
    use LogsActivityOptions;

    protected $fillable = [
        'full_name',
        'institution',
        'relationship',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
