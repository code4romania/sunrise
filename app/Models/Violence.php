<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToBeneficiary;
use App\Concerns\LogsActivityOptions;
use App\Enums\Frequency;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Violence extends Model
{
    use HasFactory;
    use BelongsToBeneficiary;
    use LogsActivity;
    use LogsActivityOptions;

    protected $fillable = [
        'violence_types',
        'violence_primary_type',
        'frequency_violence',
        'description',
    ];

    protected $casts = [
        'violence_types' => AsEnumCollection::class . ':' . \App\Enums\Violence::class,
        'violence_primary_type' => \App\Enums\Violence::class,
        'frequency_violence' => Frequency::class,
    ];
}
