<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\AggravatingFactorsSchema;
use App\Enums\Helps;
use App\Enums\RiskFactorsSchema;
use App\Enums\Ternary;
use App\Enums\VictimPerceptionOfTheRiskSchema;
use App\Enums\ViolenceHistorySchema;
use App\Enums\ViolencesTypesSchema;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RiskFactors>
 */
class RiskFactorsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $riskFactors = array_merge(
            ViolenceHistorySchema::values(),
            ViolencesTypesSchema::values(),
            RiskFactorsSchema::values(),
            VictimPerceptionOfTheRiskSchema::values(),
            AggravatingFactorsSchema::values(),
        );

        $selectedRiskFactors = [];
        foreach (fake()->randomElements($riskFactors, rand(1, \count($riskFactors))) as $riskFactor) {
            $selectedRiskFactors[$riskFactor] = [
                'value' => fake()->randomElement(Ternary::values()),
                'description' => fake()->text(100),
            ];
        }

        return [
            'risk_factors' => $selectedRiskFactors,
            'extended_family_can_provide' => fake()->randomElements(Helps::values(), rand(0, 5)),
            'friends_can_provide' => fake()->randomElements(Helps::values(), rand(0, 5)),
        ];
    }
}
