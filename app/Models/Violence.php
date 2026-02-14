<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToBeneficiary;
use App\Concerns\LogsActivityOptions;
use App\Enums\Frequency;
use App\Enums\ViolenceMeans;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Violence extends Model
{
    use BelongsToBeneficiary;
    use HasFactory;
    use LogsActivityOptions;

    protected $fillable = [
        'violence_types',
        'violence_primary_type',
        'frequency_violence',
        'violence_means',
        'violence_means_specify',
        'description',
    ];

    protected $casts = [
        'violence_types' => AsEnumCollection::class.':'.\App\Enums\Violence::class,
        'violence_primary_type' => \App\Enums\Violence::class,
        'frequency_violence' => Frequency::class,
        'violence_means' => AsEnumCollection::class.':'.ViolenceMeans::class,
    ];
}
