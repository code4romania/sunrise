<?php

declare(strict_types=1);

namespace App\Models;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Specialist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'role_id',
        'specialistable_id',
        'specialistable_type',
    ];

    protected $appends = [
        'name_role',
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

    public function specialistable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getNameRoleAttribute(): string
    {
        $this->load(['user', 'role']);

        return \sprintf('%s (%s)', $this->user?->full_name, $this->role?->name);
    }
}
