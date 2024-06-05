<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Occupation;
use App\Models\Beneficiary;
use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BeneficiaryPartner>
 */
class BeneficiaryPartnerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $city = City::query()->inRandomOrder()->first();
        $effectiveCity = City::query()->inRandomOrder()->first();
        $sameAddress = fake()->boolean();

        return [
            'beneficiary_id' => Beneficiary::inRandomOrder()->first()->id,
            'last_name' => fake()->lastName(),
            'first_name' => fake()->firstName(),
            'age' => fake()->numberBetween(10, 99),
            'occupation' => fake()->randomElement(Occupation::values()),
            'legal_residence_address' => fake()->address(),
            'legal_residence_county_id' => $city->county_id,
            'legal_residence_city_id' => $city->id,
            'same_as_legal_residence' => $sameAddress,
            'effective_residence_address' => $sameAddress ? null : fake()->address(),
            'effective_residence_county_id' => $sameAddress ? null : $effectiveCity->county_id,
            'effective_residence_city_id' => $sameAddress ? null : $effectiveCity->id,
            'observations' => fake()->text(),
        ];
    }
}
