<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Children>
 */
class ChildrenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'age' => fake()->boolean() ? fake()->numberBetween(0, 20) : null,
            'birthdate' => fake()->date(),
            'current_address' => fake()->boolean() ? fake()->address() : null,
            'status' => fake()->boolean() ? fake()->words(asText: true) : null,
        ];
    }
}
