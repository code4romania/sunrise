<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\City;
use App\Models\Intervention;
use App\Models\Organization;
use App\Models\Service;
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
        $name = fake()->company();
        $city = City::query()->inRandomOrder()->first();

        return [
            'name' => $name,
            'short_name' => preg_replace('/\b(\w)|./u', '$1', $name),
            'phone' => fake()->phoneNumber(),
            'website' => fake()->url(),

            'city_id' => $city->id,
            'county_id' => $city->county_id,
            'address' => fake()->streetAddress(),

            'reprezentative_name' => fake()->name(),
            'reprezentative_email' => fake()->safeEmail(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Organization $organization) {
            $organization->users()->attach(
                User::factory()
                    ->count(25)
                    ->sequence(fn (Sequence $sequence) => [
                        'email' => sprintf('user-%d-%d@example.com', $organization->id, $sequence->index + 1),
                    ])
                    ->create()
                    ->pluck('id')
                    ->toArray()
            );

            Service::factory()
                ->count(5)
                ->for($organization)
                ->has(
                    Intervention::factory()
                        ->count(5)
                        ->for($organization)
                )
                ->create();
        });
    }
}
