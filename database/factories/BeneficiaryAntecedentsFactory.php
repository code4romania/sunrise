<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Ternary;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BeneficiaryAntecedents>
 */
class BeneficiaryAntecedentsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'has_police_reports' => fake()->randomElement(Ternary::values()),
            'police_report_count' => fake()->numberBetween(0, 300),
            'has_medical_reports' => fake()->randomElement(Ternary::values()),
            'medical_report_count' => fake()->numberBetween(0, 300),
        ];
    }
}
