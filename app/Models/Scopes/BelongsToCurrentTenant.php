<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Models\Beneficiary;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class BelongsToCurrentTenant implements Scope
{
    /**
     * Scope all queries to the current tenant (centru = Organization).
     * In this app tenant and organization are the same: beneficiaries belong to one Organization (centru).
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (! Filament::auth()->check()) {
            return;
        }

        if (! Filament::hasTenancy()) {
            return;
        }

        $tenant = Filament::getTenant();

        if ($tenant === null) {
            if ($model instanceof Beneficiary && Filament::getCurrentPanel()?->getId() === 'organization') {
                $builder->whereRaw('1 = 0');
            }

            return;
        }

        $builder->whereBelongsTo($tenant);
    }
}
