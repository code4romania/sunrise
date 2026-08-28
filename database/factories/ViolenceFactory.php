<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Frequency;
use App\Enums\Violence;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Violence>
 */
class ViolenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = fake()->randomElements(Violence::values(), fake()->numberBetween(1, 5));

        return [
            'violence_types' => $types,
            'violence_primary_type' => $types[0],
            'frequency_violence' => fake()->randomElement(Frequency::values()),
            'description' => fake()->text(),
        ];
    }
}
