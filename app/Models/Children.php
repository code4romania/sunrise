<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToBeneficiary;
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
    ];
}
