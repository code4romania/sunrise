<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Beneficiary;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BeneficiarySituation>
 */
class BeneficiarySituationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'beneficiary_id' => Beneficiary::inRandomOrder()->first()->id,
            'moment_of_evaluation' => fake()->text(),
            'description_of_situation' => fake()->text(),
        ];
    }
}
