<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CounselingSheet;
use App\Enums\GeneralStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'counseling_sheet',
        'status',
    ];

    protected $casts = [
        'status' => GeneralStatus::class,
        'counseling_sheet' => CounselingSheet::class,
    ];

//    public function counselingSheet(): HasOne
//    {
//
//    }

    public function serviceInterventions(): HasMany
    {
        return $this->hasMany(ServiceIntervention::class);
    }

    public function interventions(): HasMany
    {
        return $this->hasMany(Intervention::class);
    }
}
