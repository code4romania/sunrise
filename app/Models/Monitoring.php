<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToBeneficiary;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Znck\Eloquent\Relations\BelongsToThrough;
use Znck\Eloquent\Traits\BelongsToThrough as BelongsToThroughTrait;

class Monitoring extends Model
{
    use HasFactory;
    use BelongsToBeneficiary;
    use BelongsToThroughTrait;

    protected $fillable = [
        'date',
        'number',
        'start_date',
        'end_date',
        'admittance_date',
        'admittance_disposition',
        'services_in_center',
        'protection_measures',
        'health_measures',
        'legal_measures',
        'psychological_measures',
        'aggressor_relationship',
        'others',
        'progress',
        'observation',
    ];

    protected $casts = [
        'protection_measures' => 'json',
        'health_measures' => 'json',
        'legal_measures' => 'json',
        'psychological_measures' => 'json',
        'aggressor_relationship' => 'json',
        'others' => 'json',
    ];

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function organization(): BelongsToThrough
    {
        return $this->belongsToThrough(Organization::class, Beneficiary::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(MonitoringChild::class);
    }

    public function specialists(): BelongsToMany
    {
        return $this->belongsToMany(CaseTeam::class, 'monitoring_specialists');
    }

    public function team()
    {
        return $this->beneficiary->team();
    }
}
