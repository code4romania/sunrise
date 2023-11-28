<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Intervention extends Model
{
    use BelongsToOrganization;
    use HasFactory;
    use HasUlids;

    public $with = ['service'];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
