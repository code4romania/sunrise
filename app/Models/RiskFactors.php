<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToBeneficiary;
use App\Enums\Helps;
use App\Enums\Level;
use App\Enums\Ternary;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskFactors extends Model
{
    use HasFactory;
    use BelongsToBeneficiary;

    protected $fillable = [
        'risk_factors',
        'extended_family_can_provide',
        'friends_can_provide',
        'risk_level',
    ];

    protected $casts = [
        'risk_factors' => 'json',
        'extended_family_can_provide' => AsEnumCollection::class . ':' . Helps::class,
        'friends_can_provide' => AsEnumCollection::class . ':' . Helps::class,
        'risk_level' => Level::class,
    ];

    protected static function boot()
    {
        parent::boot();
        self::creating(fn (RiskFactors $model) => self::calculateRiskLevel($model));

        self::updating(fn (RiskFactors $model) => self::calculateRiskLevel($model));
    }

    public static function calculateRiskLevel(self $model): void
    {
        if (self::hasHighRiskLevel($model->risk_factors)) {
            $model->risk_level = Level::HIGH;

            return;
        }

        if (self::hasMediumRiskLevel($model->risk_factors)) {
            $model->risk_level = Level::MEDIUM;

            return;
        }

        if (self::hasLowRiskLevel($model->risk_factors)) {
            $model->risk_level = Level::LOW;

            return;
        }
        $model->risk_level = Level::NONE;
    }

    private static function hasHighRiskLevel(array $riskFactors): bool
    {
        $highRiskFields = ['use_weapons_in_act_of_violence', 'death_threats', 'victim_afraid_for_himself'];
        foreach ($highRiskFields as $field) {
            if (empty($riskFactors[$field])) {
                continue;
            }
            if (Ternary::isYes($riskFactors[$field]['value'])) {
                return true;
            }
        }

        if (self::getTrueAnswersCount($riskFactors) >= 5) {
            return true;
        }

        return false;
    }

    private static function getTrueAnswersCount(array $riskFactors): int
    {
        $count = 0;
        foreach ($riskFactors as $riskFactor) {
            if (Ternary::isYes($riskFactor['value'])) {
                $count++;
            }
        }

        return $count;
    }

    private static function hasMediumRiskLevel(array $model): bool
    {
        if (self::getTrueAnswersCount($model) == 4) {
            return true;
        }

        return false;
    }

    private static function hasLowRiskLevel(array $model): bool
    {
        if (self::getTrueAnswersCount($model) >= 1) {
            return true;
        }

        return false;
    }
}
