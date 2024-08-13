<?php

declare(strict_types=1);

namespace Database\Factories;

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
            'psychological_advice' => fake()->boolean(),
            'legal_advice' => fake()->boolean(),
            'legal_assistance' => fake()->boolean(),
            'prenatal_advice' => fake()->boolean(),
            'social_advice' => fake()->boolean(),
            'medical_services' => fake()->boolean(),
            'medical_payment' => fake()->boolean(),
            'securing_residential_spaces' => fake()->boolean(),
            'occupational_program_services' => fake()->boolean(),
            'educational_services_for_children' => fake()->boolean(),
            'temporary_shelter_services' => fake()->boolean(),
            'protection_order' => fake()->boolean(),
            'crisis_assistance' => fake()->boolean(),
            'safety_plan' => fake()->boolean(),
            'other_services' => fake()->boolean(),
            'other_services_description' => fake()->text(),
            'recommendations_for_intervention_plan' => fake()->text(),
        ];
    }
}
