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
        $startDate = fake()->dateTimeBetween('-2 weeks', '2 weeks');
        $endDate = fake()->dateTimeBetween($startDate, '+3 weeks');

        return [
            'objections' => fake()->text(),
            'expected_results' => fake()->text(),
            'procedure' => fake()->text(),
            'indicators' => fake()->text(),
            'achievement_degree' => fake()->text(),
            'start_date_interval' => $startDate->format('Y-m-d'),
            'end_date_interval' => $endDate->format('Y-m-d'),
        ];
    }
}
