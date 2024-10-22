<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\AddressType;
use App\Enums\Occupation;
use App\Models\Address;
use App\Models\BeneficiaryPartner;
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
        $sameAddress = fake()->boolean();

        return [
            'last_name' => fake()->lastName(),
            'first_name' => fake()->firstName(),
            'age' => fake()->numberBetween(10, 99),
            'occupation' => fake()->randomElement(Occupation::values()),
            'same_as_legal_residence' => $sameAddress,
            'observations' => fake()->text(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (BeneficiaryPartner $beneficiaryPartner) {
            Address::factory()
                ->for($beneficiaryPartner, 'addressable')
                ->state(['address_type' => AddressType::LEGAL_RESIDENCE])
                ->create();

            if (! $beneficiaryPartner->same_as_legal_residence) {
                Address::factory()
                    ->for($beneficiaryPartner, 'addressable')
                    ->state(['address_type' => AddressType::EFFECTIVE_RESIDENCE])
                    ->create();
            }
        });
    }
}
