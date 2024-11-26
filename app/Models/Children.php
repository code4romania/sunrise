<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToBeneficiary;
use App\Enums\GenderShortValues;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Children extends Model
{
    use HasFactory;
    use BelongsToBeneficiary;

    protected $fillable = [
        'name',
        'age',
        'birthdate',
        'current_address',
        'status',
        'gender',
        'workspace',
    ];

    protected $casts = [
        'gender' => GenderShortValues::class,
    ];
}
