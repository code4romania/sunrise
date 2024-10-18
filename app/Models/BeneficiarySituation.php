<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToBeneficiary;
use App\Concerns\LogsActivityOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class BeneficiarySituation extends Model
{
    use HasFactory;
    use BelongsToBeneficiary;
    use LogsActivity;
    use LogsActivityOptions;

    protected $fillable = [
        'moment_of_evaluation',
        'description_of_situation',
    ];
}
