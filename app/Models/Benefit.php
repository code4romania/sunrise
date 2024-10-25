<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasGeneralStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Benefit extends Model
{
    use HasFactory;
    use HasGeneralStatus;

    protected $fillable = [
        'name',
    ];

    public function benefitTypes(): HasMany
    {
        return $this->hasMany(BenefitType::class);
    }
}
