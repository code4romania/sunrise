<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\AreaType;
use App\Enums\InstitutionStatus;
use App\Enums\OrganizationType;
use App\Models\City;
use App\Models\Institution;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Institution>
 */
class InstitutionFactory extends Factory
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
            'type' => fake()->randomElement(OrganizationType::values()),
            'cif' => fake()->numerify(),
            'main_activity' => fake()->text(),
            'area' => fake()->randomElement(AreaType::values()),

            'city_id' => $city->id,
            'county_id' => $city->county_id,
            'address' => fake()->streetAddress(),

            'representative_name' => fake()->name(),
            'representative_email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'website' => fake()->url(),
            'contact_person' => fake()->name(),
            'contact_person_email' => fake()->safeEmail(),
            'contact_person_phone' => fake()->phoneNumber(),
            'status' => fake()->randomElement(InstitutionStatus::values()),
        ];
    }

    public function withOrganization()
    {
        return $this->afterCreating(function (Institution $institution) {
            Organization::factory()
                ->for($institution)
                ->count(2)
                ->withUsers()
                ->withBeneficiaries()
                ->withCommunityProfile()
                ->withInterventions()
                ->create();
        });
    }
}