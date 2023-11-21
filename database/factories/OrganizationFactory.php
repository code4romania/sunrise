<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organization>
 */
class OrganizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Organization $organization) {
            $organization->users()->attach(
                User::factory()
                    ->count(5)
                    ->sequence(fn (Sequence $sequence) => [
                        'email' => sprintf('user-%d-%d@example.com', $organization->id, $sequence->index + 1),
                    ])
                    ->create()
                    ->pluck('id')
                    ->toArray()
            );
        });
    }
}
