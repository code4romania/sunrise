<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToBeneficiary;
use App\Concerns\LogsActivityOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;
    use BelongsToBeneficiary;
    use LogsActivityOptions;

    protected $fillable = [
        'specialist',
        'date',
        'location',
        'observations',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
