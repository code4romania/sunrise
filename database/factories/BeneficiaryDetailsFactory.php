<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\HomeOwnership;
use App\Enums\Income;
use App\Enums\Occupation;
use App\Enums\Studies;
use App\Enums\Ternary;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BeneficiaryDetails>
 */
class BeneficiaryDetailsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'has_family_doctor' => fake()->randomElement(Ternary::values()),
            'family_doctor_name' => fake()->name(),
            'family_doctor_contact' => fake()->phoneNumber(),
            'psychiatric_history' => fake()->randomElement(Ternary::values()),
            'psychiatric_history_notes' => fake()->text(100),
            'criminal_history' => fake()->randomElement(Ternary::values()),
            'criminal_history_notes' => fake()->text(100),
            'studies' => fake()->randomElement(Studies::values()),
            'occupation' => fake()->randomElement(Occupation::values()),
            'workplace' => fake()->text(100),
            'income' => fake()->randomElement(Income::values()),
            'elder_care_count' => fake()->numberBetween(0, 5),
            'homeownership' => fake()->randomElement(HomeOwnership::values()),
        ];
    }
}
