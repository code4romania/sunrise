<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToBeneficiary;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluateDetails extends Model
{
    use HasFactory;
    use BelongsToBeneficiary;

    protected $fillable = [
        'specialist_id',
        'registered_date',
        'file_number',
        'method_of_identifying_the_service',
    ];

    public function specialist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'specialist_id');
    }
}
