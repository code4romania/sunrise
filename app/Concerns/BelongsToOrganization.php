<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Models\Organization;
use App\Models\Scopes\BelongsToCurrentTenant;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Ensures the model is scoped by tenant (centru) and organization.
 * In this app tenant = Organization (centru); all queries are limited to the current tenant.
 */
trait BelongsToOrganization
{
    public function initializeBelongsToOrganization(): void
    {
        $this->fillable[] = 'organization_id';
    }

    protected static function bootBelongsToOrganization(): void
    {
        static::creating(function (self $model) {
            if (! Filament::auth()->check()) {
                return;
            }

            if (! Filament::hasTenancy()) {
                return;
            }

            $model->organization_id = filament()->getTenant()->id;
        });

        static::addGlobalScope(new BelongsToCurrentTenant);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
