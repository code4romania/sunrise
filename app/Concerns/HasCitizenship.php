<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Models\Country;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasCitizenship
{
    public function initializeHasCitizenship(): void
    {
        $this->fillable = array_merge($this->fillable, ['citizenship_id']);
    }

    public function citizenship(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
