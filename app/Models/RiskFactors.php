<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToBeneficiary;
use App\Concerns\LogsActivityOptions;
use App\Enums\Helps;
use App\Enums\Level;
use App\Enums\Ternary;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskFactors extends Model
{
    use BelongsToBeneficiary;
    use HasFactory;
    use LogsActivityOptions;

    protected $fillable = [
        'risk_factors',
        'extended_family_can_provide',
        'extended_family_can_not_provide',
        'friends_can_provide',
        'friends_can_not_provide',
        'risk_level',
    ];

    protected $casts = [
        'risk_factors' => 'json',
        'extended_family_can_provide' => AsEnumCollection::class.':'.Helps::class,
        'friends_can_provide' => AsEnumCollection::class.':'.Helps::class,
        'risk_level' => Level::class,
        'extended_family_can_not_provide' => Ternary::class,
        'friends_can_not_provide' => Ternary::class,
    ];

    protected static function boot()
    {
        parent::boot();
        self::creating(function (RiskFactors $model) {
            self::calculateRiskLevel($model);
        });

        self::updating(function (RiskFactors $model) {
            self::calculateRiskLevel($model);
        });
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
        foreach (self::HIGH_RISK_QUESTION_KEYS as $field) {
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

    /**
     * Question keys for high-risk questions (7, 11, 17 per legislation Annex 2).
     * CRESCUT = min 1 yes on these OR min 5 yes on all 1-23.
     * MEDIU/SCĂZUT count excludes these.
     */
    private const HIGH_RISK_QUESTION_KEYS = [
        'use_weapons_in_act_of_violence',
        'death_threats',
        'victim_afraid_for_himself',
    ];

    private static function getTrueAnswersCount(array $riskFactors): int
    {
        $count = 0;
        foreach ($riskFactors as $riskFactor) {
            if (Ternary::isYes($riskFactor['value'] ?? null)) {
                $count++;
            }
        }

        return $count;
    }

    private static function getTrueAnswersCountExcludingHighRisk(array $riskFactors): int
    {
        $count = 0;
        foreach ($riskFactors as $key => $riskFactor) {
            if (in_array($key, self::HIGH_RISK_QUESTION_KEYS, true)) {
                continue;
            }
            if (Ternary::isYes($riskFactor['value'] ?? null)) {
                $count++;
            }
        }

        return $count;
    }

    private static function hasMediumRiskLevel(array $riskFactors): bool
    {
        return self::getTrueAnswersCountExcludingHighRisk($riskFactors) >= 4;
    }

    private static function hasLowRiskLevel(array $riskFactors): bool
    {
        return self::getTrueAnswersCountExcludingHighRisk($riskFactors) >= 1;
    }
}
