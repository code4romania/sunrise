<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\AggressorLegalHistory;
use App\Enums\AggressorRelationship;
use App\Enums\CivilStatus;
use App\Enums\Drug;
use App\Enums\Gender;
use App\Enums\Occupation;
use App\Enums\Studies;
use App\Enums\Ternary;
use App\Enums\Violence;
use App\Models\Aggressor;
use App\Models\Beneficiary;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Aggressor>
 */
class AggressorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'beneficiary_id' => Beneficiary::factory(),

            'relationship' => fake()->randomElement(AggressorRelationship::values()),
            'age' => fake()->randomNumber(2),
            'gender' => fake()->randomElement(Gender::values()),
            'civil_status' => fake()->randomElement(CivilStatus::values()),
            'studies' => fake()->randomElement(Studies::values()),
            'occupation' => fake()->randomElement(Occupation::values()),
            'has_violence_history' => fake()->randomElement(Ternary::values()),
            'has_psychiatric_history' => fake()->randomElement(Ternary::values()),
            'has_drug_history' => fake()->randomElement(Ternary::values()),
            'legal_history' => fake()->randomElements(AggressorLegalHistory::values()),
            'has_protection_order' => fake()->randomElement(Ternary::values()),
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Aggressor $aggressor) {
            if (Ternary::isYes($aggressor->has_violence_history)) {
                $aggressor->violence_types = fake()->randomElements(Violence::values());
            }

            if (Ternary::isYes($aggressor->has_psychiatric_history)) {
                $aggressor->psychiatric_history_notes = fake()->sentence();
            }

            if (Ternary::isYes($aggressor->has_drug_history)) {
                $aggressor->drugs = fake()->randomElements(Drug::values());
            }

            if (Ternary::isYes($aggressor->has_protection_order)) {
                $aggressor->protection_order_notes = fake()->sentence();
            }
        });
    }
}
