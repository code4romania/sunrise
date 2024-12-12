<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\InterventionPlan;
use App\Models\InterventionService;
use App\Models\Organization;
use App\Models\OrganizationService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InterventionPlan>
 */
class InterventionPlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'admit_date_in_center' => $this->faker->date(),
            'plan_date' => $this->faker->date(),
            'last_revise_date' => $this->faker->date(),
        ];
    }

    public function withService(Organization $organization): static
    {
        return $this->afterCreating(function (InterventionPlan $plan) use ($organization) {
            InterventionService::factory()
                ->for($plan)
                ->state(fn () => [
                    'organization_service_id' => OrganizationService::query()
                        ->where('organization_id', $organization->id)
                        ->inRandomOrder()
                        ->first()
                        ->id,
                    'specialist_id' => $plan->beneficiary->specialistsTeam->random()->id,
                ])
                ->withBeneficiaryIntervention()
                ->count(rand(1, 5))
                ->create();
        });
    }
}
