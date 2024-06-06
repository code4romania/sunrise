<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EvaluateDetails>
 */
class EvaluateDetailsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'registered_date' => fake()->date(),
            'file_number' => fake()->randomNumber(),
            'method_of_identifying_the_service' => fake()->text(),
        ];
    }
}
