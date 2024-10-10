<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ResidenceEnvironment;
use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $city = City::query()->inRandomOrder()->first();

        return [
            'address' => fake()->address(),
            'county_id' => $city->county_id,
            'city_id' => $city->id,
            'environment' => fake()->randomElement(ResidenceEnvironment::values()),
        ];
    }
}
