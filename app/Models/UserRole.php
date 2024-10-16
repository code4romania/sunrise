<?php

declare(strict_types=1);

namespace App\Models;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserRole extends Pivot
{
    public static function booted(): void
    {
        static::creating(function ($record) {
            $record->organization_id = Filament::getTenant()?->id;
        });
    }
}
