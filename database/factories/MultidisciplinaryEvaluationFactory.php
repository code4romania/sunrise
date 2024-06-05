<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Applicant;
use App\Models\Beneficiary;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MultidisciplinaryEvaluation>
 */
class MultidisciplinaryEvaluationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $applicant = fake()->randomElement(Applicant::values());

        return [
            'beneficiary_id' => Beneficiary::inRandomOrder()->first()->id,
            'applicant' => $applicant,
            'reporting_by' => Applicant::OTHER->is($applicant) ? fake()->text() : null,
            'medical_need' => fake()->text(),
            'professional_need' => fake()->text(),
            'emotional_and_psychological_need' => fake()->text(),
            'social_economic_need' => fake()->text(),
            'legal_needs' => fake()->text(),
            'extended_family' => fake()->text(),
            'family_social_integration' => fake()->text(),
            'income' => fake()->text(),
            'community_resources' => fake()->text(),
            'house' => fake()->text(),
            'risk' => fake()->text(),
        ];
    }
}
