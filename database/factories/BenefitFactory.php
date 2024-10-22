<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Benefit;
use App\Models\BenefitType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Benefit>
 */
class BenefitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'status' => fake()->boolean(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Benefit $benefit) {
            BenefitType::factory()
                ->for($benefit)
                ->count(5)
                ->create();
        });
    }
}
