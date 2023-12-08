<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\CivilStatus;
use App\Enums\Gender;
use App\Enums\IDType;
use App\Enums\ResidenceEnvironment;
use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Beneficiary>
 */
class BeneficiaryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $birthdate = fake()
            ->dateTimeBetween('1900-01-01', '2099-12-31')
            ->format('Y-m-d');

        $gender = fake()->randomElement(Gender::values());

        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'prior_name' => fake()->boolean(25) ? fake()->lastName() : null,

            'civil_status' => fake()->randomElement(CivilStatus::values()),

            'gender' => $gender,
            'birthplace' => fake()->sentence(),
            'birthdate' => $birthdate,

            'primary_phone' => fake()->phoneNumber(),
            'backup_phone' => fake()->boolean(25) ? fake()->phoneNumber() : null,
        ];
    }

    public function withCNP(): static
    {
        return $this->state(fn (array $attributes) => [
            'cnp' => rescue(
                fn () => fake()->cnp(gender: $attributes['gender'], dateOfBirth: $attributes['birthdate']),
                fn () => fake()->cnp(dateOfBirth: $attributes['birthdate']),
                false
            ),
        ]);
    }

    public function withID(): static
    {
        return $this->state(fn (array $attributes) => [
            'id_type' => fake()->randomElement(IDType::values()),
            'id_serial' => fake()->lexify('??'),
            'id_number' => fake()->numerify('######'),
        ]);
    }

    public function withLegalResidence(): static
    {
        return $this->state(function (array $attributes) {
            $city = City::query()->inRandomOrder()->first();

            return [
                'legal_residence_address' => fake()->address(),
                'legal_residence_county_id' => $city->county_id,
                'legal_residence_city_id' => $city->id,
                'legal_residence_environment' => fake()->randomElement(ResidenceEnvironment::values()),
                'same_as_legal_residence' => true,
            ];
        });
    }

    public function withEffectiveResidence(): static
    {
        return $this->state(function (array $attributes) {
            $city = City::query()->inRandomOrder()->first();

            return [
                'effective_residence_address' => fake()->address(),
                'effective_residence_county_id' => $city->county_id,
                'effective_residence_city_id' => $city->id,
                'effective_residence_environment' => fake()->randomElement(ResidenceEnvironment::values()),
                'same_as_legal_residence' => false,
            ];
        });
    }

    public function withContactNotes(): static
    {
        return $this->state(fn (array $attributes) => [
            'contact_notes' => fake()->paragraphs(asText: true),
        ]);
    }
}
