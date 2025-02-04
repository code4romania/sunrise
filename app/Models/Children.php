<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToBeneficiary;
use App\Concerns\HasBirthdate;
use App\Concerns\LogsActivityOptions;
use App\Enums\GenderShortValues;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Children extends Model
{
    use HasFactory;
    use BelongsToBeneficiary;
    use HasBirthdate;
    use LogsActivityOptions;

    protected $fillable = [
        'name',
        'current_address',
        'status',
        'gender',
        'workspace',
    ];

    protected $casts = [
        'gender' => GenderShortValues::class,
    ];

    public function getAgeAttribute(): int | string | null
    {
        $age = $this->birthdate ? Carbon::parse($this->birthdate)->diffInYears(now()) : null;

        return $age === 0 ? '<1' : \intval($age);
    }
}
