<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToOrganization;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserRole extends Pivot
{
    use BelongsToOrganization;

    public function getTable()
    {
        return 'user_roles';
    }

    public static function booted(): void
    {
        static::creating(function ($record) {
            if (! $record->organization_id && Filament::getTenant()?->id) {
                $record->organization_id = Filament::getTenant()?->id;
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
