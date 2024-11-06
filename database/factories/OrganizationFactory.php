<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Beneficiary;
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

        return [
            'name' => $name,
            'short_name' => preg_replace('/\b(\w)|./u', '$1', $name),
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
                        'institution_id' => $sequence->index === 0 ? $organization->institution_id : null,
                        'ngo_admin' => $sequence->index === 0,
                    ])
                    ->withRolesAndPermissions($organization->id)
                    ->create()
                    ->pluck('id')
                    ->toArray()
            );

            $organization->users()->attach(
                User::factory()
                    ->count($count)
                    ->sequence(fn (Sequence $sequence) => [
                        'email' => \sprintf('invited-%d-%d@example.com', $organization->id, $sequence->index + 1),
                    ])
                    ->invited()
                    ->create()
                    ->pluck('id')
                    ->toArray()
            );

            $organization->users()->attach(
                User::factory()
                    ->count($count)
                    ->deactivated()
                    ->sequence(fn (Sequence $sequence) => [
                        'email' => \sprintf('deactivated-%d-%d@example.com', $organization->id, $sequence->index + 1),
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

    public function withBeneficiaries(int $count = 30): static
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
