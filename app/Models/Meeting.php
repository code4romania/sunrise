<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToBeneficiary;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;
    use BelongsToBeneficiary;

    protected $fillable = [
        'specialist',
        'date',
        'location',
        'observations',
    ];
}
