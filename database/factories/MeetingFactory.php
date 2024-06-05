<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Beneficiary;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Meeting>
 */
class MeetingFactory extends Factory
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
            'specialist' => fake()->text(),
            'date' => fake()->date(),
            'location' => fake()->text(),
            'observations' => fake()->text(),
        ];
    }
}
