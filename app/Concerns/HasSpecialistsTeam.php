<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Models\Specialist;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasSpecialistsTeam
{
    public function specialistsMembers(): MorphToMany
    {
        return $this->morphToMany(User::class, 'specialistable', 'specialists', 'specialistable_id');
    }

    public function specialistsTeam(): HasMany
    {
        return $this->hasMany(Specialist::class, 'specialistable_id')
            ->where('specialistable_type', $this->getMorphClass());
    }
}
