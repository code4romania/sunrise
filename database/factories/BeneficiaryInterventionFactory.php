<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BeneficiaryIntervention>
 */
class BeneficiaryInterventionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'objections' => fake()->text(),
            'expected_results' => fake()->text(),
            'procedure' => fake()->text(),
            'indicators' => fake()->text(),
            'achievement_degree' => fake()->text(),
            'start_date_interval' => fake()->date(),
            'end_date_interval' => fake()->date(),
        ];
    }
}
