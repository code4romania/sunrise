<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToOrganization
{
    public function initializeBelongsToOrganization(): void
    {
        $this->fillable[] = 'organization_id';
    }

    protected static function bootBelongsToOrganization(): void
    {
        static::creating(function (self $model) {
            if (! auth()->check()) {
                return;
            }

            $model->organization_id = auth()->user()->organization_id;
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
