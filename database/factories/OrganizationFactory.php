<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\OrganizationType;
use App\Models\Beneficiary;
use App\Models\City;
use App\Models\CommunityProfile;
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
            'type' => fake()->randomElement(OrganizationType::values()),
            'phone' => fake()->phoneNumber(),
            'website' => fake()->url(),

            'city_id' => $city->id,
            'county_id' => $city->county_id,
            'address' => fake()->streetAddress(),

            'reprezentative_name' => fake()->name(),
            'reprezentative_email' => fake()->safeEmail(),
        ];
    }

    public function withUsers(int $count = 5): static
    {
        return $this->afterCreating(function (Organization $organization) use ($count) {
            $organization->users()->attach(
                User::factory()
                    ->count($count)
                    ->sequence(fn (Sequence $sequence) => [
                        'email' => \sprintf('user-%d-%d@example.com', $organization->id, $sequence->index + 1),
                    ])
                    ->create()
                    ->pluck('id')
                    ->toArray()
            );
        });
    }

    public function withCommunityProfile(): static
    {
        return $this->afterCreating(function (Organization $organization) {
            CommunityProfile::factory()
                ->for($organization)
                ->create();
        });
    }

    public function withBeneficiaries(int $count = 50): static
    {
        return $this->afterCreating(function (Organization $organization) use ($count) {
            Beneficiary::factory()
                ->count($count)
                ->withCNP()
                ->withID()
                ->withLegalResidence()
                ->withEffectiveResidence()
                ->withContactNotes()
                ->withChildren()
                ->withAntecedents()
                ->withFlowPresentation()
                ->withBeneficiaryDetails()
                ->withCitizenship()
                ->withEthnicity()
                ->for($organization)
                ->create();
        });
    }

    public function withInterventions(int $count = 5): static
    {
        return $this->afterCreating(function (Organization $organization) use ($count) {
            Service::query()
                ->inRandomOrder()
                ->limit($count)
                ->get()
                ->each(
                    fn (Service $service) => Intervention::factory()
                        ->count($count)
                        ->for($organization)
                        ->for($service)
                        ->create()
                );
        });
    }
}
