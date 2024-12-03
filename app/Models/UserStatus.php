<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToOrganization;
use App\Concerns\HasUserStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserStatus extends Model
{
    use HasFactory;
    use HasUserStatus;
    use BelongsToOrganization;

    protected $fillable = [
        'user_id',
        'status',
    ];

    protected $casts = [
        'status' => \App\Enums\UserStatus::class,
    ];
}
