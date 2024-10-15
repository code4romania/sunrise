<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ChildAggressorRelationship;
use App\Enums\MaintenanceSources;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MonitoringChild>
 */
class MonitoringChildFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'aggressor_relationship' => $this->faker->randomElement(ChildAggressorRelationship::values()),
            'maintenance_sources' => $this->faker->randomElement(MaintenanceSources::values()),
            'location' => $this->faker->text(100),
            'observations' => $this->faker->text(),
        ];
    }
}
