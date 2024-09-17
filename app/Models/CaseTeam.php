<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToBeneficiary;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaseTeam extends Model
{
    use HasFactory;
    use BelongsToBeneficiary;

    protected $fillable = [
        'user_id',
        'roles',
    ];

    protected $casts = [
        'roles' => 'json',
    ];

    protected $with = [
        'user',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getRolesStringAttribute()
    {
        $roleIds = $this->attributes['roles'] ?? [];

        if (\is_string($roleIds)) {
            $roleIds = json_decode($roleIds, true);
        }

        return Role::whereIn('id', $roleIds)
            ->active()
            ->get()
            ->pluck('name');
    }
}
