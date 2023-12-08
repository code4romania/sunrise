<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Models\Ethnicity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasEthnicity
{
    public function initializeHasEthnicity(): void
    {
        $this->fillable = array_merge($this->fillable, ['ethnicity_id']);
    }

    public function ethnicity(): BelongsTo
    {
        return $this->belongsTo(Ethnicity::class);
    }
}
