<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\LogsActivityOptions;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Specialist extends Model
{
    use HasFactory;
    use LogsActivityOptions;

    protected $fillable = [
        'user_id',
        'role_id',
        'specialistable_id',
        'specialistable_type',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)
            ->whereHas('organizations', fn (Builder $query) => $query->where('organization_id', Filament::getTenant()->id));
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class)
            ->active();
    }

    /**
     * Role relationship without active scope, for display when the role may be inactive.
     * Use when eager loading to avoid lazy load violations.
     */
    public function roleForDisplay(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function specialistable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Display name with role. Requires user and roleForDisplay to be eager loaded.
     */
    public function getNameRoleAttribute(): string
    {
        $user = $this->relationLoaded('user') ? $this->user : null;
        $role = $this->relationLoaded('roleForDisplay') ? $this->roleForDisplay : null;

        return \sprintf('%s (%s)', $user?->full_name ?? '—', $role?->name ?? '—');
    }
}
