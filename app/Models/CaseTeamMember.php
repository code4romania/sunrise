<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToBeneficiary;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaseTeamMember extends Model
{
    use HasFactory;
    use BelongsToBeneficiary;

    protected $fillable = [
        'user_id',
        'role_id',
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

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
