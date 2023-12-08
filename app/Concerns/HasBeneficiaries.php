<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Models\Beneficiary;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasBeneficiaries
{
    public function beneficiaries(): HasMany
    {
        return $this->hasMany(Beneficiary::class);
    }
}
