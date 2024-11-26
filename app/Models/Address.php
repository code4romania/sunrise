<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ResidenceEnvironment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'county_id',
        'city_id',
        'address',
        'environment',
        'address_type',
        'addressable_id',
        'addressable_type',
    ];

    protected $casts = [
        'environment' => ResidenceEnvironment::class,
    ];

    public function county(): BelongsTo
    {
        return $this->belongsTo(County::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }
}
