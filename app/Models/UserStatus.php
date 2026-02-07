<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToOrganization;
use App\Concerns\HasUserStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Znck\Eloquent\Relations\BelongsToThrough;
use Znck\Eloquent\Traits\BelongsToThrough as BelongsToThroughTrait;

class UserStatus extends Model
{
    use BelongsToOrganization;
    use BelongsToThroughTrait;
    use HasFactory;
    use HasUserStatus;

    protected $fillable = [
        'user_id',
        'status',
    ];

    protected $casts = [
        'status' => \App\Enums\UserStatus::class,
    ];

    public function institution(): BelongsToThrough
    {
        return $this->belongsToThrough(Institution::class, Organization::class);
    }
}
