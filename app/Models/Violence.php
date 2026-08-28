<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToBeneficiary;
use App\Concerns\LogsActivityOptions;
use App\Enums\Frequency;
use App\Enums\Violence as ViolenceKind;
use App\Enums\ViolenceMeans;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Violence extends Model
{
    use BelongsToBeneficiary;
    use HasFactory;
    use LogsActivityOptions;

    protected static function booted(): void
    {
        static::saving(function (self $violence): void {
            $types = $violence->violence_types;
            if ($types === null || $types->isEmpty()) {
                return;
            }

            $first = $types->first();
            if ($first instanceof ViolenceKind) {
                $violence->violence_primary_type = $first;
            }
        });
    }

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
