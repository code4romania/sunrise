<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Ternary;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DetailedEvaluationResult>
 */
class DetailedEvaluationResultFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'psychological_advice' => fake()->randomElement(Ternary::values()),
            'legal_advice' => fake()->randomElement(Ternary::values()),
            'legal_assistance' => fake()->randomElement(Ternary::values()),
            'prenatal_advice' => fake()->randomElement(Ternary::values()),
            'social_advice' => fake()->randomElement(Ternary::values()),
            'medical_services' => fake()->randomElement(Ternary::values()),
            'medical_payment' => fake()->randomElement(Ternary::values()),
            'securing_residential_spaces' => fake()->randomElement(Ternary::values()),
            'occupational_program_services' => fake()->randomElement(Ternary::values()),
            'educational_services_for_children' => fake()->randomElement(Ternary::values()),
            'temporary_shelter_services' => fake()->randomElement(Ternary::values()),
            'protection_order' => fake()->randomElement(Ternary::values()),
            'crisis_assistance' => fake()->randomElement(Ternary::values()),
            'safety_plan' => fake()->randomElement(Ternary::values()),
            'other_services' => fake()->randomElement(Ternary::values()),
            'other_services_description' => fake()->text(),
            'recommendations_for_intervention_plan' => fake()->text(),
        ];
    }
}
