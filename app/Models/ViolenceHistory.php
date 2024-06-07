<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToBeneficiary;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViolenceHistory extends Model
{
    use HasFactory;
    use BelongsToBeneficiary;

    protected $fillable = [
        'date_interval',
        'significant_events',
    ];
}
