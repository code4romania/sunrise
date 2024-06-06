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
        return [
            'violence_types' => fake()->randomElements(Violence::values(), rand(0, 5)),
            'violence_primary_type' => fake()->randomElement(Violence::values()),
            'frequency_violence' => fake()->randomElement(Frequency::values()),
            'description' => fake()->text(),
        ];
    }
}
