<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceIntervention;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(asText: true),
            'status' => fake()->boolean(),
        ];
    }

    public function configure(): static
    {
        return $this
            ->afterCreating(function (Service $service) {
                ServiceIntervention::factory()
                    ->for($service)
                    ->count(5)
                    ->create();
            });
    }
}
