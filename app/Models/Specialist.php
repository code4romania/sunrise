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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)
            ->whereHas('organizations', fn (Builder $query) => $query->where('organization_id', Filament::getTenant()->id));
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function specialistable(): MorphTo
    {
        return $this->morphTo();
    }
}
